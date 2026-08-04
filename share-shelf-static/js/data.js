// Share Shelf — static demo data
// This mirrors real sample data from share_shelf.sql. There's no backend
// here, so this file stands in for the database.

const CATEGORIES = [
  { id: 1, name: "Electronics", parent: null },
  { id: 2, name: "Books", parent: null },
  { id: 3, name: "Clothing", parent: null },
  { id: 4, name: "Home & Kitchen", parent: null },
  { id: 5, name: "Sports & Outdoors", parent: null },
  { id: 6, name: "Beauty & Personal Care", parent: null },
  { id: 7, name: "Toys & Games", parent: null },
  { id: 8, name: "Furniture", parent: null },
  { id: 9, name: "Stationery", parent: null },
  { id: 10, name: "Medical Equipment", parent: null },
  { id: 11, name: "Mobile Phones", parent: 1 },
  { id: 12, name: "Laptops", parent: 1 },
  { id: 13, name: "Electronic Accessories", parent: 1 },
  { id: 14, name: "Academic Books", parent: 2 },
  { id: 15, name: "Novels", parent: 2 },
  { id: 16, name: "Men's Clothing", parent: 3 },
  { id: 17, name: "Women's Clothing", parent: 3 },
  { id: 18, name: "Baby Clothing", parent: 3 },
  { id: 19, name: "Kitchen Appliances", parent: 4 },
  { id: 20, name: "Home Decor", parent: 4 },
  { id: 21, name: "Fitness Equipment", parent: 5 },
  { id: 22, name: "Outdoor Gear", parent: 5 },
  { id: 23, name: "Makeup", parent: 6 },
  { id: 24, name: "Perfumes", parent: 6 },
];

const ITEMS = [
  { id: 1, title: "Dell Inspiron 15 Laptop", desc: "Intel Core i5, 8GB RAM, 256GB SSD. Minor scratches. Includes original charger.", condition: "Good", type: "Sale", price: 35000, qty: 1, pickup: "Rampura", category: 12, seller: "Imran Kabir", rating: 4.5 },
  { id: 2, title: "Samsung Galaxy A32", desc: "64GB, 6GB RAM. Includes charger and protective case.", condition: "Like New", type: "Sale", price: 18000, qty: 1, pickup: "Agrabad", category: 11, seller: "Tanvir Hasan", rating: 5 },
  { id: 3, title: "Logitech M185 Wireless Mouse", desc: "Unused. Original packaging included.", condition: "New", type: "Donation", price: 0, qty: 1, pickup: "Badda", category: 13, seller: "Arif Mahmud", rating: 4 },
  { id: 4, title: "HP Pavilion 14 Laptop", desc: "Intel Core i5, 8GB RAM, 512GB SSD. Includes charger.", condition: "Good", type: "Sale", price: 32000, qty: 1, pickup: "Bashundhara", category: 12, seller: "Sumaiya Yasmin Nairit", rating: 4.8 },
  { id: 5, title: "Engineering Mathematics by Kreyszig", desc: "10th Edition. No missing pages. Some highlighted sections.", condition: "Good", type: "Sale", price: 450, qty: 1, pickup: "Kotwali", category: 14, seller: "Nafis Islam", rating: 4.2 },
  { id: 6, title: "HSC Physics 2nd Paper", desc: "Latest edition. Excellent condition with clean pages.", condition: "Like New", type: "Donation", price: 0, qty: 1, pickup: "Kazla", category: 14, seller: "Fahim Uddin", rating: 4.6 },
  { id: 7, title: "Harry Potter and the Philosopher's Stone", desc: "Paperback edition. Pages are clean.", condition: "Like New", type: "Sale", price: 300, qty: 1, pickup: "Pabna Sadar", category: 15, seller: "Sumaiya Akter", rating: 4.9 },
  { id: 8, title: "Men's Denim Jacket", desc: "Blue denim jacket. Size: L. No tears or stains.", condition: "Good", type: "Sale", price: 800, qty: 1, pickup: "Mohammadpur", category: 16, seller: "Tania Khan", rating: 4.3 },
  { id: 9, title: "Women's Cotton Kurti", desc: "Maroon cotton kurti. Size: M. Worn only twice.", condition: "Like New", type: "Donation", price: 0, qty: 1, pickup: "Boyra", category: 17, seller: "Tasnia Noor", rating: 4.7 },
  { id: 10, title: "Baby Winter Jacket", desc: "Warm jacket for babies aged 1-2 years. Size: 90 cm.", condition: "Good", type: "Donation", price: 0, qty: 1, pickup: "Daulatpur", category: 18, seller: "Raida Tasnim", rating: 5 },
  { id: 11, title: "Philips Rice Cooker", desc: "1.8L capacity. Fully functional with inner pot.", condition: "Good", type: "Donation", price: 0, qty: 1, pickup: "Boalia", category: 19, seller: "Sadia Islam", rating: 4.1 },
  { id: 13, title: "Decathlon Yoga Mat", desc: "6 mm thick exercise mat. Clean and well maintained.", condition: "Good", type: "Sale", price: 750, qty: 1, pickup: "Barishal Sadar", category: 21, seller: "Rakib Hossain", rating: 4.4 },
  { id: 14, title: "Maybelline Fit Me Foundation", desc: "Shade 128 Warm Nude. Unopened. Purchased wrong shade.", condition: "New", type: "Sale", price: 700, qty: 1, pickup: "Nathullabad", category: 23, seller: "Nairuz Jafrin", rating: 4.6 },
  { id: 15, title: "Lattafa Yara Eau de Parfum", desc: "100 ml sealed bottle. Original packaging included.", condition: "New", type: "Sale", price: 2600, qty: 1, pickup: "Bashundhara", category: 24, seller: "Sumaiya Yasmin Nairit", rating: 4.8 },
  { id: 16, title: "iPhone SE (2020)", desc: "64GB, Black. Includes original charger and box. Battery health 87%.", condition: "Good", type: "Sale", price: 22000, qty: 1, pickup: "Mirpur", category: 11, seller: "Nusrat Jahan", rating: 4.5 },
  { id: 17, title: "JBL Tune 510BT Headphones", desc: "Wireless Bluetooth headphones. Includes charging cable.", condition: "Like New", type: "Sale", price: 3200, qty: 1, pickup: "Fatullah", category: 13, seller: "Sabbir Ahmed", rating: 4.7 },
];

function categoryName(id) {
  const c = CATEGORIES.find(c => c.id === id);
  return c ? c.name : "Uncategorized";
}

function findItem(id) {
  return ITEMS.find(i => i.id === Number(id));
}

function mainCategories() {
  return CATEGORIES.filter(c => c.parent === null);
}

function subCategories(parentId) {
  return CATEGORIES.filter(c => c.parent === Number(parentId));
}

// A "main" category also covers items in any of its subcategories.
function itemsInCategory(categoryId) {
  categoryId = Number(categoryId);
  const cat = CATEGORIES.find(c => c.id === categoryId);
  if (!cat) return ITEMS;
  if (cat.parent === null) {
    const childIds = CATEGORIES.filter(c => c.parent === categoryId).map(c => c.id);
    return ITEMS.filter(i => i.category === categoryId || childIds.includes(i.category));
  }
  return ITEMS.filter(i => i.category === categoryId);
}
