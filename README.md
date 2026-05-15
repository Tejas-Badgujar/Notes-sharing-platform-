# Notes Sharing Platform - UI Design

This is the UI/UX frontend mockups for the Neo-Futuristic Notes Sharing Platform. It has been built with a strict PHP structure for easy backend integration.

## Features Built
- **Neo-Futuristic Glassmorphism:** Uses deep navy, violet, and neon cyan tones.
- **Animations:** 60fps optimized animations using CSS variables, transforms, and opacity.
- **Interactive Elements:** Cursor glow, 3D card tilt effects, magnetic buttons, and a global loading spinner.
- **Architecture:** One global CSS (`css/style.css`), one global JS (`js/script.js`), and reusable components in `includes/`.

## Directory Structure
- `config/` - Empty (Backend to implement)
- `uploads/` - Empty (File storage)
- `auth/` - Login and Register pages.
- `user/` - User Dashboard, Upload, and My Notes pages.
- `admin/` - Admin Dashboard and Approvals pages.
- `ajax/` - Mock AJAX UI handlers for Live Search and Filtering.
- `includes/` - Reusable headers, footers, navbars, sidebars.
- `css/` - Global styling.
- `js/` - Global interactivity.
- `index.php` - Main landing page.

## How to View
Run a local PHP server in this directory:
```bash
php -S localhost:8000
```
Then visit `http://localhost:8000` in your browser.

## Backend Integration Notes
- Form actions currently point to other PHP pages to simulate the flow. Replace these with proper routing/controller logic.
- The `includes/header.php` and `includes/sidebar.php` files use a `$level` variable to resolve relative paths (e.g. `../` vs `./`).
- Live search logic is mocked in `ajax/live_search_ui.php`. Integrate this with real database queries.
