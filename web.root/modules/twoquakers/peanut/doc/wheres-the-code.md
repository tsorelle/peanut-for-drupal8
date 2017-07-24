#Code and Configuration Locations for Peanut and Tops

- **Initialization**\* in ```src/EventSubscriber/PeanutSubscriber``` as configured in ```pnut_tops.services.yml```
- **Routing definitions** in ```pnut_tops.routing.yml```
- **Routing implementations** in ```src/Controller/PeanutController.php```
- **JS Library definitions** in ```peanut/pnut_tops.libraries.yml```
- **Attach JS Libraries** in ```pnut_tops.module::pnut_tops_page_attachments_alter()```
- **Insert view name for templates** in ```pnut_tops.module::pnut_tops_preprocess_html```
- **Insert MVVM View content** in ```pnut_tops.module::pnut_tops_preprocess_node```
- **Tops source**\*\* in ```tops/```
- **Tops extension classes for Drupal 8** ```Tops/drupal8```
- **Peanut JS/TS files**\*\*\* ```pnut\```
- **Peanut 3rd Party Packages**\*\*\* ```pnut\packages```

\* Initialization includes autoloading and tops initializations for paths, configuration, etc.

\*\* Except for the extension classes in ```Tops/drupal8``` files are imported from the Tops project. 
https://github.com/tsorelle/tops

\*\*\* Except for ```pnut\packages```, all files are imported from the Peanut2017 project. https://github.com/tsorelle/peanut2017






