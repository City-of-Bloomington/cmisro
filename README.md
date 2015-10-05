## CMIS Read-Only

This module provides a simple interface for Drupal users to display content from a CMIS server. Content creators reference documents in the CMIS system, and the module will list the documents in the HTML page, along with links to download the files.

The module is read-only. This means no documents are created, updated, or deleted on the CMIS server. Also, the documents are not cached or stored in Drupal. By forgoing all of the writing, we avoid all the complexity of synchronization between the two systems. There are no caches to get out of date, and no files to update as upstream documents change.

Drupal content creators will be able to reference CMIS documents by ID, folder, or using a raw CMIS query.

### Settings

The CMIS settings are listed under Web Services in Drupal's configuration screen.
CMIS Repository URL

This must point to the browser binding url of your CMIS server. For instance, if you have alfresco installed at http://localhost, then the CMIS Repository URL for that alfresco instance would be:

http://localhost/alfresco/api/-default-/public/cmis/versions/1.1/browser

If you have multiple repositories set up on that CMIS server, you might need to adjust the URL accordingly to match your server.
CMIS Repository ID

Since you *could* have multiple repositories set up on your CMIS server, this is a required field. It usually matches information in the repository URL.

For Alfresco installs, where you just have a single repository, and you haven't renamed it, this is usually "-default-".

### CMIS Username & Password

Most CMIS servers require a username and password. This means you will probably want to go to your CMIS Server and create a user for Drupal to connect as. This gives you a way to control what is available to be displayed on your Drupal website.

Visibility is the important permission here. Drupal will be connecting to your CMIS server on behalf of everyone out there in the world who wants to see and download something from you.

For example, if you're using Alfresco, we are not using any Alfresco "Publishing" features. The only thing that matters is what that Drupal user is allowed to access. In Alfresco terms, we make sure Drupal is in the "Consumers".
