=== WPIntegrations Form Connector for Elementor and RD Station ===
Contributors: wpintegrations
Tags: rd station, elementor, leads, forms, conversions
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Send Elementor Pro form submissions to RD Station Marketing as conversion events, with field mapping and lead attribution.

== Description ==

**WPIntegrations Form Connector** sends every Elementor Pro form submission to RD Station Marketing as a conversion event — automatically, and with the lead source attribution intact.

= Why this plugin =

* **Attribution preserved** — the visitor's RD Station tracking cookies (`rdtrk`, `__trf.src`) are forwarded with each conversion, so your funnel and traffic source reports stay accurate, exactly like a native RD Station form.
* **Field mapping UI** — map your Elementor form fields to RD Station fields in a modern admin interface, custom fields from your own account included. Field names are suggested automatically after the first submission.
* **Doesn't hold up your form** — on hosts running PHP-FPM or LiteSpeed (most of them), delivery happens after the visitor already has their confirmation, so a slow RD Station doesn't slow your form down.
* **LGPD ready** — map your form's consent checkbox and the plugin sends the corresponding legal basis with each conversion.
* **Full delivery log** — see every request, response and failure. Nothing is silent.
* **Secure by design** — your OAuth tokens are stored encrypted, every admin endpoint requires administrator capability, and nothing is ever sent anywhere except RD Station's API.

= What this free version includes =

* Unlimited integrations, each sending an Elementor form to RD Station Marketing
* Unlimited mapped fields per integration
* Your own conversion identifier per integration
* Your account's real custom fields offered in the mapping screen, with field names suggested after the first submission
* LGPD consent mapping and RD Station attribution cookies forwarded with every conversion
* A delivery log with the full request and response for every send

= What the Pro version adds =

* **RD Station CRM as a destination** — send submissions straight into your CRM as contacts, matched on email so a returning visitor updates the record they already have, with companies and deals alongside
* Tags, funnel stage, lead owner and the opportunity flag
* A delivery queue that retries failed sends automatically

[Learn more about the Pro version](https://wpintegrations.net/plugins/connect-elementor-forms-to-rd-station/)

= Source code and third-party libraries =

The complete, human-readable source of this plugin is public at [github.com/wpintegrations/wpintegrations-connector-for-elementor-rd-station](https://github.com/wpintegrations/wpintegrations-connector-for-elementor-rd-station).

The admin screens are a React app. The compiled bundle in `build/assets/` is generated with Vite from the source in `resources/js/`, which ships with the plugin together with `package.json` and `vite.config.mjs`. To rebuild it, install Node.js 18 or later and pnpm, then run `pnpm install` and `pnpm run build` in the plugin folder.

The bundle includes these open-source libraries:

* React and React DOM 18.3.1 (MIT) — [github.com/facebook/react](https://github.com/facebook/react)
* Ant Design 5.29.3 (MIT) — [github.com/ant-design/ant-design](https://github.com/ant-design/ant-design)
* Ant Design Icons 5.6.1 (MIT) — [github.com/ant-design/ant-design-icons](https://github.com/ant-design/ant-design-icons)

This plugin is built by WPIntegrations and is not affiliated with, endorsed by, or sponsored by Elementor Ltd. or RD Station. "Elementor" and "RD Station" are the trademarks of their respective owners, used here only to describe what the plugin connects.

= Requirements =

* Elementor Pro (for the Forms widget)
* An app registered in the RD Station App Store, with the RD Station Marketing permissions

== External services ==

This plugin connects to the **RD Station APIs** (operated by RD Station, https://www.rdstation.com/). Nothing is sent until you create an integration, and then only when a mapped Elementor form is submitted.

**RD Station Marketing** — delivers your submissions as conversion events.

* **What is sent:** the form fields you explicitly map (such as name, email, phone), the conversion identifier, and — if present — the visitor's RD Station tracking values for lead attribution: the visitor ID from the `rdtrk` cookie and the traffic source from the `__trf.src` cookie. If you map them, it also sends the optional values the mapping screen offers: the page URL, the submission time, the logged-in user's name and email, the site title and URL, and any fixed text you enter.
* **What is read:** your account's contact fields, so the mapping screen can offer your real custom fields instead of asking you to type identifiers.
* **Connection test:** the Test connection button sends one conversion with the identifier `wpi-connection-test` and no contact data.
* **Where:** `https://api.rd.services/platform/events` and `https://api.rd.services/platform/contacts/fields`, authenticated with the OAuth token from your own RD Station app.

**RD Station authentication** — used only while connecting an account and when an access token is renewed. Your app's Client ID and Client Secret, the authorization code and the refresh token are sent to `https://api.rd.services/auth/dialog` and `https://api.rd.services/auth/token`.

No data is sent to WPIntegrations or any other third party. RD Station terms of use: https://legal.rdstation.com/en/rdstation-mp-services-agreement/ — privacy notice: https://legal.rdstation.com/en/privacy-policy/

== Installation ==

1. Install and activate the plugin.
2. Create an app in the RD Station App Store with the RD Station Marketing permissions, register the callback URL shown on the plugin's Settings tab, then paste the app's Client ID and Client Secret there and connect.
3. Open the **Integrations** tab and create an integration: pick your Elementor form, then map the fields and save.

== Frequently Asked Questions ==

= Does it work with the free version of Elementor? =

The Forms widget is an Elementor Pro feature, so Elementor Pro is required.

= Which RD Station plans are supported? =

Any RD Station Marketing plan with API access. You only need to register your own app in the App Store and give it the Marketing permissions.

= Can it send submissions to RD Station CRM instead? =

No. This plugin delivers conversion events to RD Station Marketing. Sending to RD Station CRM is part of the separate Pro plugin.

= Which fields do I have to map? =

The same one RD Station itself marks as required on its own forms: an **email**. Any custom field your account marks mandatory is added to that list automatically. Everything else is optional.

= What happens if RD Station is temporarily unavailable? =

The submission is not delivered, and the failure is recorded in the Logs tab with the full response.

= Where is the source code of the admin screens? =

In `resources/js/` inside the plugin, and publicly at [github.com/wpintegrations/wpintegrations-connector-for-elementor-rd-station](https://github.com/wpintegrations/wpintegrations-connector-for-elementor-rd-station). The compiled files in `build/` are generated from it with Vite: run `pnpm install` and then `pnpm run build` in the plugin folder. See "Source code and third-party libraries" above for the bundled libraries.

= Where do I see errors? =

The **Logs** tab shows every delivery with the full request and response.

== Screenshots ==

1. Where a form's submissions go: RD Station Marketing conversion events.
2. Name the integration and pick the Elementor form, found on your published pages, templates and popups.
3. Map the form's fields to RD Station's, your account's own custom fields included.
4. The Logs tab records every delivery, with the full request and response.
5. Settings: connect your RD Station Marketing app, and choose how long delivery logs are kept.

== Changelog ==

= 1.0.2 =
* The saved Client Secret now shows its last characters, so you can tell which one is saved.
* A clearer Pro section on the Settings page.

= 1.0.1 =
* Fixed: the plugin now steps aside for the Pro version under its new folder name, so the two never run at the same time.

= 1.0.0 =
* Initial release: Elementor Pro Forms → RD Station Marketing conversion events, field mapping with your account's custom fields, LGPD consent mapping, RD Station attribution cookies, a delivery log, and encrypted credential storage.
