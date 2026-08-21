# Customization Guide - Namsa Flora

This guide will help you easily customize the look and feel of your flower shop.

## 🎨 Color Customization

### Primary Colors
Edit the CSS variables in `resources/views/layouts/app.blade.php`:

```css
:root {
    --primary-color: #d63384;  /* Change this to your brand color */
    --secondary-color: #ffc107;
    --success-color: #28a745;
}
```

**Common color examples:**
- Pink: `#d63384` (current)
- Red: `#dc3545`
- Purple: `#6f42c1`
- Blue: `#0d6efd`
- Green: `#198754`

### Where Colors Are Used
- Logo icon
- Active navigation links
- Cart badge
- Hover effects

---

## 📐 Layout Customization

### Product Grid Columns

In `resources/views/layouts/app.blade.php`, find `.products-grid`:

```css
.products-grid {
    grid-template-columns: repeat(4, 1fr); /* 4 columns on desktop */
}
```

**Change to:**
- 3 columns: `repeat(3, 1fr)`
- 5 columns: `repeat(5, 1fr)`
- 6 columns: `repeat(6, 1fr)`

### Product Card Spacing

Adjust the gap between products:

```css
.products-grid {
    gap: 30px; /* Change this value */
}
```

---

## 🖼️ Product Card Customization

### Product Image Size

```css
.product-image {
    height: 280px; /* Adjust height */
    object-fit: cover; /* or 'contain' to show full image */
}
```

### Product Card Styling

```css
.product-card {
    background: white; /* Card background */
    border-radius: 8px; /* Corner roundness */
    /* Add border: border: 1px solid #e0e0e0; */
}
```

### Buy Now Button

```css
.buy-now-btn {
    background: #e0e0e0; /* Button color */
    color: #333; /* Text color */
    padding: 12px; /* Button height */
}
```

**Make it colorful:**
```css
.buy-now-btn {
    background: var(--primary-color);
    color: white;
}
```

---

## 📝 Text Customization

### Logo Text

In `resources/views/layouts/app.blade.php`, find:

```html
<a href="{{ route('home') }}" class="logo">
    <i class="bi bi-flower1"></i> Namsa Flora
</a>
```

Change "Namsa Flora" to your shop name.

### Navigation Links

```html
<ul class="nav-links">
    <li><a href="{{ route('home') }}">Home</a></li>
    <li><a href="{{ route('products.index') }}">Products</a></li>
    <li><a href="#contact">Contact Us</a></li>
    <li><a href="#terms">Terms and Conditions</a></li>
</ul>
```

Add or remove links as needed.

---

## 🎯 Font Customization

### Change Font Family

```css
body {
    font-family: 'Your Font', sans-serif;
}
```

**Popular font options:**
- Google Fonts: Add to `<head>`:
  ```html
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  ```
  Then use: `font-family: 'Poppins', sans-serif;`

### Font Sizes

```css
.product-name {
    font-size: 16px; /* Product name size */
}
.product-price {
    font-size: 18px; /* Price size */
}
```

---

## 📱 Responsive Breakpoints

The layout automatically adjusts at these screen sizes:

- **Desktop**: 4 columns (1024px+)
- **Tablet**: 3 columns (768px - 1024px)
- **Mobile**: 2 columns (480px - 768px)
- **Small Mobile**: 1 column (< 480px)

To change breakpoints, edit the `@media` queries in the CSS.

---

## 🖼️ Image Customization

### Default Placeholder

When products don't have images, they show a placeholder. To change:

In `resources/views/products/index.blade.php`:

```html
<img src="https://via.placeholder.com/300x280?text={{ urlencode($product->name) }}" ...>
```

Change `300x280` to your preferred dimensions.

### Image Upload Settings

In `app/Http/Controllers/Admin/ProductController.php`:

```php
'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
```

- Change `max:2048` to allow larger files (in KB)
- Add more formats: `mimes:jpeg,png,jpg,gif,webp`

---

## 🎨 Header Customization

### Header Background

```css
.header {
    background: white; /* Change to any color */
    /* Or use gradient: */
    /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
}
```

### Header Height

```css
.header {
    padding: 20px 0; /* Increase for taller header */
}
```

---

## 🔍 Search Functionality

The search box searches in:
- Product name
- Product description
- Product category

To add more search fields, edit `app/Http/Controllers/ProductController.php`:

```php
$q->where('name', 'like', '%' . $request->search . '%')
  ->orWhere('description', 'like', '%' . $request->search . '%')
  ->orWhere('category', 'like', '%' . $request->search . '%');
  // Add more fields here
```

---

## 📄 Footer Customization

In `resources/views/layouts/app.blade.php`:

```html
<footer>
    <div class="container text-center">
        <p>&copy; {{ date('Y') }} Namsa Flora. All rights reserved.</p>
        <p class="mb-0">Beautiful flowers for every occasion</p>
    </div>
</footer>
```

Change the text to match your needs.

---

## 🎯 Quick Style Tips

### Make Cards More Spacious
```css
.product-info {
    padding: 25px; /* Increase padding */
}
```

### Add Shadows
```css
.product-card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
```

### Change Hover Effect
```css
.product-card:hover {
    transform: scale(1.02); /* Slight zoom instead of lift */
}
```

---

## 📦 File Locations

- **Main Layout**: `resources/views/layouts/app.blade.php`
- **Product Listing**: `resources/views/products/index.blade.php`
- **Product Controller**: `app/Http/Controllers/ProductController.php`
- **Admin Product Controller**: `app/Http/Controllers/Admin/ProductController.php`

---

## 💡 Pro Tips

1. **Test Changes**: Always test on different screen sizes after making changes
2. **Backup First**: Make a copy of files before major changes
3. **Use Browser DevTools**: Right-click → Inspect to see CSS in action
4. **Color Picker**: Use online tools like coolors.co to find matching colors
5. **Keep It Simple**: The current design is intentionally simple - don't overcomplicate it

---

## 🆘 Need Help?

- Check the main `README.md` for setup instructions
- Review `TESTING_GUIDE.md` for testing tips
- All styles are in one place: `resources/views/layouts/app.blade.php`

Happy customizing! 🌸
