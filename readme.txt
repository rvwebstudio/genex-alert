Installation
==============================================================

1. Download the plugin zip file.
2. In your WordPress admin panel, go to Plugins > Add New.
3. Click Upload Plugin.
4. Choose the plugin zip file and click Install Now.
5. After installation, click Activate Plugin.

Configuration
==============================================================
1. Go to Settings > Genex Alert in your WordPress admin panel.
2. Site Type:
	2.1 Hub Site: Select this option if this site will be the central location for creating and managing alerts.
	2.2 Client Site: Select this option if this site will receive alerts from a Hub Site.
3. Hub URL (Client Site Only): If you selected "Client Site," enter the URL of your Hub Site here.
4. Hub API Key (Client Site Only): If you selected "Client Site," enter the API key for your Hub Site here.
5. Click Save Settings.


Usage
==============================================================
Creating Alerts (Hub Site)

1. Go to Alert Banners in your WordPress admin panel.
2. Click Add New.
3. Title: Enter a title for your alert.
4. Content: Enter the message you want to display.
5. Alert Banner Details:
	5.1 Start Datetime: Set the date and time when the alert should start.
	5.2 End Datetime: Set the date and time when the alert should end.
	5.3 Status: Choose "Active" or "Inactive."
6. Click Publish.


Viewing Alerts (Client and Hub Sites)
==============================================================
- Active alerts will appear in a banner at the top of the page for logged-in users.
- Click "View all active messages" to see all active alerts in an offcanvas panel.


Managing Alerts (Hub Site)
==============================================================
- Go to Alert Banners to manage your alerts.


Client Site Synchronization
==============================================================
- Client sites automatically fetch active alerts from the Hub Site.