# MPHB Amenities Extended

Extend MotoPress Hotel Booking plugin functionality with enhanced amenities management and display.

## Description

MPHB Amenities Extended adds the ability to attach images/icons to accommodation amenities (taxonomy: `mphb_room_type_facility`) and display them elegantly on the front-end using shortcodes.

Features include:

- Amenity image upload and display in admin term edit screens
- Shortcode `[amenities]` to list amenities for the current accommodation
- Shortcode `[amenities_tree]` to show hierarchical amenities in tree format
- Output caching via transients for performance

## Installation

1. Upload the plugin folder `mphb-amenities-extended` to `/wp-content/plugins/`
2. Activate the plugin via the WordPress admin Plugins page
3. Navigate to **Accommodation → Amenities** to add or edit amenities and upload images
4. Use shortcodes `[amenities]` and `[amenities_tree]` in your accommodation templates or pages

## Usage

- `[amenities]` — Lists up to 10 amenities with images and names for the current accommodation.
- `[amenities_tree]` — Displays amenities in a hierarchical tree view with images and names.
![Screenshot 1](https://raw.githubusercontent.com/skazgr/mphb-amenities-extended/main/Screenshot_1.png)  
*Amenities list displayed on front-end [amenities]*

![Screenshot 2](https://raw.githubusercontent.com/skazgr/mphb-amenities-extended/main/Screenshot_2.png)  
*Amenities list displayed on front-end [amenities_tree]*

## Frequently Asked Questions

**Q: Does this plugin work without MotoPress Hotel Booking?**  
A: No. This plugin is an extension and requires MotoPress Hotel Booking to function.

## Changelog

### 1.0.0
- Initial release with amenities image upload and shortcode display functionality.

## License

This plugin is licensed under the GNU General Public License v3.0 (GPLv3).  
You may use, modify, and distribute it under the terms of this license.  
See [https://www.gnu.org/licenses/gpl-3.0.html](https://www.gnu.org/licenses/gpl-3.0.html) for details.

---

© 2025 Marios Progoulakis  