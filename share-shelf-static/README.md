# Share Shelf — Static Demo (for GitHub Pages)

This is a **UI-only** mirror of the Share Shelf site — no PHP, no MySQL,
nothing server-side. It's meant to satisfy the "live GitHub Pages link"
requirement for your Web UI milestone. The real, working app (with an
actual database) is the separate `share-shelf` PHP project you already
have for XAMPP.

## What's real vs. what's simulated

- **Sample items/categories** are hard-coded in `js/data.js`, pulled from
  your actual `share_shelf.sql` sample data so it looks authentic.
- **"Login"** just stores a name in your browser's `localStorage` — typing
  any name and clicking Login "logs you in" for that browser. There's no
  password check, no real account.
- **Cart, listings you submit, admin approve/reject clicks**, etc. all
  update local browser state or just show a confirmation message. None of
  it is saved anywhere beyond your own browser, and it resets if you clear
  browser data.
- Refreshing the page keeps your demo "login" and cart (that's what
  `localStorage` is for), but nothing syncs between different browsers or
  devices — each visitor gets their own independent, fake session.

## How to publish it on GitHub Pages

### 1. Create a GitHub repository (skip if you already have one for this project)
- Go to github.com → New repository → name it (e.g. `share-shelf`) → Create.

### 2. Push these files to it
From inside this `share-shelf-static` folder:

```bash
git init
git add .
git commit -m "Static UI demo for GitHub Pages"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
git push -u origin main
```

(If your repo already has your PHP project in it, put these static files
in a separate folder like `/docs` or a separate branch — see step 4b.)

### 3. Turn on GitHub Pages
1. On GitHub, open your repository → **Settings** → **Pages** (left
   sidebar, under "Code and automation").
2. Under **Build and deployment → Source**, choose **Deploy from a
   branch**.
3. Under **Branch**, choose `main` and folder `/ (root)` — or `/docs` if
   you put the static files in a docs folder — then **Save**.
4. Wait a minute or two, then refresh the page. GitHub will show you the
   live URL, something like:
   ```
   https://YOUR_USERNAME.github.io/YOUR_REPO/
   ```

### 4. If you want both the PHP project and this static demo in one repo
Two common approaches:
- **(a) Separate folders:** put the PHP app in `/app` (or wherever) and
  this static demo in `/docs`, then point GitHub Pages at `/docs` in step
  3 above.
- **(b) Separate branches:** keep `main` for your PHP code, and push this
  static folder to a branch called `gh-pages`, then point GitHub Pages at
  that branch instead.

Either way, only the static HTML/CSS/JS files will ever actually render
on the live link — GitHub Pages silently ignores `.php` files (it can't
run them), so don't rely on the PHP folder being reachable there.

## Testing it locally before you push
Since it's just static files, you can open `index.html` directly in a
browser, or serve it locally for a closer match to how GitHub Pages will
behave:

```bash
cd share-shelf-static
python3 -m http.server 8000
```

then visit `http://localhost:8000`.

## Pages included

Home, Login, Register, hidden Admin Login (`alogin.html`, not linked
anywhere — same as the real app), Browse (with search + category filter),
Item Details, List an Item (with the same main→sub category cascading
select as the PHP version), Cart, Checkout, My Listings, My Purchases, My
Claims, Profile (with reviews + deactivate account), Support, and the
full Admin section (Dashboard, Listings approval, Users, Reports,
Tickets).
