# Genex Alert Banner Plugin

A WordPress plugin for displaying important messages or alerts to logged-in users.

## Description

The Genex Alert Banner plugin allows you to easily create and manage alerts that are displayed to logged-in users on your WordPress website. It supports two modes:

*   **Hub Site:** A central site for creating and managing alerts.
*   **Client Site:** A site that receives and displays alerts from a designated Hub Site.

## Key Features

*   **Centralized Alert Management (Hub Site):** Create, edit, and manage all your alerts from a single location.
*   **Automatic Alert Distribution (Client Site):** Client sites automatically receive and display active alerts from the Hub Site.
*   **Scheduled Alerts:** Set start and end dates/times for your alerts to control when they are displayed.
*   **Active/Inactive Status:** Easily activate or deactivate alerts.
*   **Customizable Display:** Alerts are displayed in a clean, unobtrusive banner at the top of the page.
*   **Offcanvas View:** Users can click a link to view all active alerts in a slide-out panel.
*   **Loader:** A loading indicator is displayed while fetching alerts.
*   **WordPress Toolbar:** WordPress toolbar is hidden when offcanvas is active.
*   **Logged-in Users Only:** Alerts are only visible to logged-in users.
*   **Custom Post Type (Hub Site):** Alerts are managed through a dedicated custom post type called "Alert Banners."
*   **Settings Page:** A settings page allows you to configure the plugin and define whether the site is a Hub or Client.

## Installation

1.  Download the plugin zip file.
2.  In your WordPress admin panel, go to **Plugins > Add New**.
3.  Click **Upload Plugin**.
4.  Choose the plugin zip file and click **Install Now**.
5.  After installation, click **Activate Plugin**.

## Configuration

1.  Go to **Settings > Genex Alert** in your WordPress admin panel.
2.  **Site Type:**
    *   **Hub Site:** Select this option if this site will be the central location for creating and managing alerts.
    *   **Client Site:** Select this option if this site will receive alerts from a Hub Site.
3.  **Hub URL (Client Site Only):** If you selected "Client Site," enter the URL of your Hub Site here.
4.  **Hub API Key (Client Site Only):** If you selected "Client Site," enter the API key for your Hub Site here.
5.  Click **Save Settings**.

## Usage

### Creating Alerts (Hub Site)

1.  Go to **Alert Banners** in your WordPress admin panel.
2.  Click **Add New**.
3.  **Title:** Enter a title for your alert.
4.  **Content:** Enter the message you want to display.
5.  **Alert Banner Details:**
    *   **Start Datetime:** Set the date and time when the alert should start.
    *   **End Datetime:** Set the date and time when the alert should end.
    *   **Status:** Choose "Active" or "Inactive."
6.  Click **Publish**.

### Viewing Alerts (Client and Hub Sites)

*   Active alerts will appear in a banner at the top of the page for logged-in users.
*   Click "View all active messages" to see all active alerts in an offcanvas panel.

### Managing Alerts (Hub Site)

*   Go to **Alert Banners** to manage your alerts.

### Client Site Synchronization

*   Client sites automatically fetch active alerts from the Hub Site.

## Contributing

This plugin is a test project created by Ravi Bhatia for GenexMarketing.com. At this time, contributions are not being accepted.

## License

This plugin is a test project and is not currently released under an open-source license. It is intended for internal use by GenexMarketing.com.