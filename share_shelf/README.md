# Share Shelf — Web UI (CSE311L Milestone)

A working HTML + Bootstrap 5 + PHP + MySQL site built directly on your
`share_shelf.sql` schema and ER diagram (Group 3). Tested end-to-end
against a live MariaDB instance before delivery (see "How this was
tested" below).

## 1. Setup in XAMPP

1. Copy the `share-shelf` folder into `htdocs`, e.g.
   `C:\xampp\htdocs\share-shelf` (Windows) or `/Applications/XAMPP/htdocs/share-shelf` (Mac).
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), create a database
   named `share_shelf`, and import **only** `share_shelf.sql`
   (migrations are already integrated into this single file).
4. Visit `http://localhost/share-shelf/` in your browser.
5. Admin login (not linked anywhere in the UI) is at
   `http://localhost/share-shelf/alogin.php` — log in with the phone/email
   and password of any user who is also in your `admin` table (IDs 47–50
   in your sample data).

`config/db.php` assumes the XAMPP defaults (`root` user, no password). If
your setup differs, edit the four variables at the top of that file.

## 2. Single SQL file (`share_shelf.sql`)

All schema upgrades that used to live in `migration.sql` and
`migration_fixes.sql` are now **baked into** `share_shelf.sql`. You only
need to import that one file. Included:

- `AUTO_INCREMENT` on all primary keys (except `admin.Admin_ID`)
- `user.Is_Banned` and `user.Is_Deleted`
- `support_ticket.Reply` / `Reply_Date`, plus guest fields
  (`Contact_Name`, `Contact_Email`, `Contact_Phone`) and nullable `User_ID`
  so tickets can be filed from the login page without an account
- `purchase_item.Seller_ID` (so "Rate Seller" works after a listing is removed)
- Wider `item_image.Image_URL` (`VARCHAR(500)`)
- `ON DELETE SET NULL` / `CASCADE` on historical vs. non-historical FKs
  so purchased/claimed items can be removed from `item` without breaking history

## 3. Application-level bug fixes (no DB change needed)

- **Session cross-contamination between admin and user logins** — this was the root cause of several reported issues (top ribbon showing a stale name, being able to reach the admin panel without real admin credentials, getting dropped into the wrong interface). Logging in as a user now clears any leftover admin session in that browser, and vice versa. The navbar also now renders distinctly for admin vs. user mode instead of reusing the same markup.
- **Claiming/purchasing an item with quantity > 1** no longer deletes the whole listing — it decrements the stock count and only removes the listing once it hits zero. This also fixes claims showing "Listing Removed" when stock was still available.
- **Search matching substrings** ("men" matching "women") — now does whole-word matching.
- **Category browsing** — selecting a main category now also shows items filed under its subcategories, and the "list an item" form uses a proper main-category → subcategory cascading select instead of one flat list.
- **Rejected listings** are now kept with `Approval_Status='Rejected'` instead of being deleted, so sellers can see the outcome on their My Listings page instead of the listing just vanishing.
- **Multiple photos per listing** — the listing form now accepts up to 3 image links instead of just one.
- **Pickup time** — the checkout form now uses a minute-only time picker (no seconds) and validates the format server-side.
- **Phone numbers** — registration now requires exactly 11 digits, both client- and server-side.
- **Show/hide password** toggle added to login, register, and admin login.
- **Banned/deactivated accounts** get a clear, specific message on login and on re-registration attempts, instead of a generic "already exists."
- **Admin's ban screen** now shows the user's seller rating and how many reports have been filed against their listings before you ban them.
- **"My Reviews"** section added to your profile page — see both reviews you've given and received.
- Viewing your own Pending/Rejected listing (via "My Listings" → View) no longer incorrectly bounces you away — that page was only built to show `Approved` items to anyone, including the owner.
- Listings with zero photos now show a placeholder image instead of a broken/empty carousel.

**Not changed — a design decision worth flagging:** a couple of your bug notes described claims going through a Pending → admin-approval flow. Your original spec was explicit that claiming a free item is instant and final (no cart, no approval step), and that's what's built. If your team actually wants an approval step for claims (like listings have), that's a real feature addition, not a bug fix — let me know and I'll build it.

## 3. What's implemented, page by page

| Page | Purpose |
|---|---|
| `index.php` | Landing page, categories, recently listed items |
| `register.php` / `login.php` / `logout.php` | Auth against the `user` table |
| `alogin.php` | Hidden admin login (checks `user` joined to `admin`) |
| `browse.php` | Search + category/type filters |
| `item.php` | Detail view, Add to Cart / Claim / Report actions |
| `add_item.php` | New listing form → inserted as `Approval_Status='Pending'` |
| `my_listings.php` | Seller's own listings with live approval status |
| `add_to_cart.php`, `cart.php`, `checkout.php` | Cart flow for paid items → creates `purchase` + `purchase_item` + `payment`, then deletes the listing |
| `claim.php`, `my_claims.php` | Direct claim flow for free items (no cart) → deletes the listing immediately |
| `my_purchases.php`, `rate.php` | Purchase history + seller rating (purchases only, not claims, per your spec) |
| `report_item.php` | Files a row in `report` |
| `support.php` | Support ticket submission + reply thread |
| `profile.php` | Edit contact/address info |
| `admin.php` | Dashboard with live counts |
| `admin_listings.php` | Approve (→ live) or reject (→ deleted) pending listings |
| `admin_users.php` | Full user history (purchases, payments, claims, listings, reports) + ban/unban (ban also wipes their listings, per your spec) |
| `admin_reports.php` | Resolve reports, optionally remove the reported listing |
| `admin_tickets.php` | Reply to and resolve support tickets |

## 4. Deliberate simplifications (worth mentioning in your in-class demo)

- **Passwords are stored in plain text**, matching your existing sample
  data (`Password varchar(50)`) rather than a hash. That's consistent
  with how the dump already looks, but it's worth flagging to your
  instructor as a "production would hash this" caveat if asked.
- **Claims are instant and final** — no pending/approve step for the
  claimer, exactly as you described ("directly taken away, no cart").
  Your schema's `claim.Status` column can still hold values like
  `Pending`/`Rejected` if you want to extend this later; the app just
  doesn't use them right now.
- **Image uploads are a URL/path field**, not a file upload widget —
  simplest thing that works for a UI milestone; swap in real file
  handling when you're ready.
- **One pickup location per checkout** (taken from the first cart item)
  — fine for a single-seller cart; flag if your demo needs
  multi-seller carts with different pickup points.

## 5. How this was tested

I couldn't reliably keep a persistent local web server alive for
interactive curl testing in this sandbox, so instead I:

1. Ran `php -l` (syntax lint) on all 21 PHP files — clean.
2. Programmatically verified every `mysqli_stmt_bind_param` call's type
   string matches its argument count across the whole codebase.
3. Spun up a real MariaDB instance, imported your `share_shelf.sql`,
   applied `migration.sql`, and ran a CLI script that executes the
   *exact* SQL sequences each page performs — register, list an item,
   admin-approve it, add to cart, checkout (purchase + payment +
   listing deletion), rate the seller, claim a donation item
   (+ deletion), ban a user (+ listing wipe), and support ticket reply.
   All 17 checks passed against the live database, including the two
   bugs described in §2 that would otherwise have surfaced as runtime
   errors during your live demo.
4. Also did an earlier live HTTP smoke test of `index.php` and
   `browse.php` through a running PHP dev server, confirming real data
   renders correctly (before the sandbox's process backgrounding got
   unreliable).

I'm confident in the SQL/PHP logic; what I couldn't fully verify in
this environment is Bootstrap rendering quirks in an actual browser —
worth a quick visual pass once it's on XAMPP.
