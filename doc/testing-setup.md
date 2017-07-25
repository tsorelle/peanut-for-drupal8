#Set up Peanut Testing For Drupal 8
1. Install Drupal 8 latest
1. Copy the application directory into the drupal root directory from the distribution or github. 
1. Copy the modules\twoquakers directory to the same location in the drupal root from the distribution or github. 
1. Install and activate Peanut module
1. Download a Bootstrap compatible theme to the themes directory
1. Modify or sub-theme the bootstrap theme for Peanut (see below) and activate.
1. Create a page, no content needed, with the URL \TestPage
1. Add TestPage to the main menu if desired.

##Adapting a Bootstrap Theme

The adaptations for peanut are very small and can be done with any bootstrap compatible theme.
For my example I used https://www.drupal.org/project/bootstrap

Generally speaking it is a good idea to create a sub-theme, rather than modify the original.
The Peanut theme provided in the distribution (or github) is such a sub-theme.  However, there seems to 
be a bug in the CDN starter kit which breaks the display of the top navbar.  The LESS or SASS versions might
work but they need to be compiled.

Instead, I simply copied two files from the Peanut theme templates/system directory into the same location 
in the Bootstrap theme.  You can use these files as examples for template modifications in other themes.

In ```html.html.twig```, I added the following code just above the final </html> tag. 
```Javascript
  {% if knockoutviewname %}
    <script>
        Peanut.PeanutLoader.startApplication('{{ knockoutviewname }}');
    </script>
  {% endif %}
```

In ```status-messages.html.twig```, I added the following markup right at the end.
```html
<div id='service-messages-container'><service-messages></service-messages></div>
```
