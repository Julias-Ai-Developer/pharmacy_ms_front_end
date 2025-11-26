# Pharmacy Management System - HTML Version

## Overview
This is a **pure HTML/CSS/JavaScript** version of the Pharmacy Management System. All PHP code and database functionality have been removed and replaced with dummy data.

## What Changed

### ✅ Removed
- All PHP code and includes
- Database connections and queries
- Authentication system
- Server-side form processing
- All backend actions

### ✅ Added
- Static HTML pages for all modules
- Dummy data embedded in JavaScript
- Client-side only functionality
- Direct page-to-page navigation

## File Structure

```
pharmacy_ms_html/
├── index.html              # Dashboard (main page)
├── login.html              # Login page (no authentication)
├── pos.html                # Point of Sale with dummy products
├── inventory.html          # Inventory list with dummy data
├── receipt.html            # Sales receipts
├── selling-units.html      # Selling units module
├── purchases.html          # Purchases module
├── prescriptions.html      # Prescriptions module
├── patients.html           # Patients module
├── clinic.html             # Clinic module
├── reports.html            # Reports module
├── users.html              # Users & Roles module
├── settings.html           # Settings module
├── assets/
│   ├── css/
│   │   └── style.css       # All styles (unchanged)
│   └── js/
│       └── main.js         # Main JavaScript (unchanged)
└── README_HTML.md          # This file
```

## How to Use

1. **Open the application**: Simply open `index.html` in your web browser
2. **Login**: The login page (`login.html`) will redirect to dashboard without authentication
3. **Navigate**: Use the sidebar to navigate between different modules
4. **Data**: All data shown is dummy/static data for demonstration

## Features

### Working Features
- ✅ Responsive design
- ✅ Dark/Light theme toggle
- ✅ Sidebar navigation
- ✅ All page layouts
- ✅ POS cart functionality (client-side only)
- ✅ Search and filter (on dummy data)

### Non-Working Features (Removed)
- ❌ Database operations
- ❌ User authentication
- ❌ Form submissions to server
- ❌ Data persistence
- ❌ Real-time updates
- ❌ PDF generation
- ❌ Reports with real data

## Dummy Data

The following modules have dummy data embedded:

- **Dashboard**: Stats cards with sample numbers, top selling products table
- **POS**: 8 sample products with prices and stock
- **Inventory**: 5 sample medicines
- **Receipts**: 3 sample transactions

Other modules show placeholder content.

## Technical Notes

- No server required - runs entirely in the browser
- No build process needed
- Compatible with all modern browsers
- Uses Bootstrap 5 and Bootstrap Icons from CDN
- Theme preference saved in localStorage

## Deployment

Since this is pure HTML, you can:
1. Open directly from file system
2. Host on any static web server
3. Deploy to GitHub Pages, Netlify, Vercel, etc.

## Original PHP Version

The original PHP version with database functionality is still available in the `views/`, `actions/`, `config/`, and `includes/` directories.

## Support

This HTML version is for demonstration purposes only. For a production system, use the full PHP version with proper database backend.

---

**Created**: November 25, 2025  
**Version**: 1.0.0 (Static HTML)
