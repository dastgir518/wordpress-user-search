# 🎓 WordPress Course Filter by Speaker

This WordPress plugin snippet provides a dynamic AJAX-powered speaker search and course filter system using shortcodes. It allows users to search speakers, select one, and instantly see all related course listings without reloading the page.

## 🔧 Features

* 🔍 AJAX-based speaker search and live suggestions
* 🎯 Filter courses authored by selected speakers
* 📦 Works with `job_listing` post type and `_case27_listing_type = courses`
* 💡 Includes styled course cards and author details
* 📱 Responsive layout with modern design

---

## 🚀 Usage

### 1. Add the Shortcode

Place this shortcode in any WordPress post or page:

```php
[course_filter_search]
```

### 2. Course Post Requirements

Make sure your course posts meet these conditions:

* Post type: `job_listing`
* Post meta key `_case27_listing_type` must be `'courses'`

---

## 📦 Installation

1. Add the provided code into your `functions.php` file or wrap it as a plugin.
2. Ensure your theme or plugin enqueues jQuery.
3. Use the `[course_filter_search]` shortcode anywhere you want the filter to appear.

---

## 📚 How It Works

### Speaker Search

* On focus, loads all speakers with published `courses`.
* Users can search by display name or email.
* AJAX call: `search_speakers`

### Course Filter

* When a speaker is selected, courses are loaded via AJAX.
* Courses are filtered based on the selected author and shown in a grid.
* AJAX call: `filter_courses`

### Speaker Loading

* AJAX call: `get_all_speakers`
* Retrieves all users with published `courses`.

---

## 🛠️ Developer Notes

### AJAX Hooks Used

```php
wp_ajax_search_speakers
wp_ajax_nopriv_search_speakers
wp_ajax_get_all_speakers
wp_ajax_nopriv_get_all_speakers
wp_ajax_filter_courses
wp_ajax_nopriv_filter_courses
```

### Nonce Verification

Used for all AJAX requests to ensure secure communication.

---

## 📁 File Structure (if separated)

You may consider organizing the logic into:

```
- /includes
  - ajax-handlers.php
  - shortcode-render.php
  - helpers.php
- course-filter.php (main plugin file)
```
