Getting Started With Symbb
==========================


# Api

## Frontend Api

### Frontend Api Routes

## Backend Api

### Backend Api Service

We have the following Api Services to access the Api Functionality also in the Code:

**symbb.core.api.site**

| Method | Parameters | Return |
| ------------- | ----------- | ----------- |
| find | integer | Symbb\Core\SiteBundle\Site |
| findAll | | array(Symbb\Core\SiteBundle\Site) |
| create | Symbb\Core\SiteBundle\Site | Symbb\Core\SiteBundle\Site |
| update | Symbb\Core\SiteBundle\Site | Symbb\Core\SiteBundle\Site |
| delete | Symbb\Core\SiteBundle\Site | boolean |

**symbb.core.api.site.navigation**

| Method | Parameters | Return |
| ------------- | ----------- | ----------- |
| find | integer | Symbb\Core\SiteBundle\Navigation |
| findAll | | array(Symbb\Core\SiteBundle\Navigation) |
| create | Symbb\Core\SiteBundle\Navigation | Symbb\Core\SiteBundle\Navigation |
| update | Symbb\Core\SiteBundle\Navigation | Symbb\Core\SiteBundle\Navigation |
| delete | Symbb\Core\SiteBundle\Navigation | boolean |
| findItem | integer | Symbb\Core\SiteBundle\Navigation\Item |
| findAllItems | | array(Symbb\Core\SiteBundle\Navigation\Item) |
| createItem | Symbb\Core\SiteBundle\Navigation\Item | Symbb\Core\SiteBundle\Navigation\Item |
| updateItem | Symbb\Core\SiteBundle\Navigation\Item | Symbb\Core\SiteBundle\Navigation\Item |
| deleteItem | Symbb\Core\SiteBundle\Navigation\Item | boolean |

### Backend Api Routes

#### POST Body Structure

```json
data: []
```

you need to pass e.g you object for saving into the data key of the Post Body

#### API Response Structure

```json
breadcrumbItems: []
callbacks: []
data: []
messages: []
success: true
user: {}
```

**breadcrumbItems**

Some information about your current position in the System

**callbacks**

some stuff that should be execeute (deprecaded)


**data**

the result of your Api Call

*find/findAll*

data is the object or a list of objects

*create/update calls*

data is the complete Object

*delete*

data is empty

**messages**

array of messages of the System like error, success, informations ( e.g if you have passed to much field while saving, then you get a info about this but the object will be saved )

#### Sites

| Name | Pattern | HTTP | POST Body |
| ------------- | ----------- | ----------- | ----------- |
| symbb_backend_api_site_list | /api/sites | GET |  |
| symbb_backend_api_site_save | /api/sites/{site} | POST | JSON |
| symbb_backend_api_site_data | /api/sites/{site} | GET |  |
| symbb_backend_api_site_delete | /api/sites/{site} | DELETE |  |
| symbb_backend_api_site_navigation_list | /api/sites/{site}/navigations | GET |  |
| symbb_backend_api_site_navigation_save | /api/sites/{site}/navigations/{navigation} | POST | JSON |
| symbb_backend_api_site_navigation_delete | /api/sites/{site}/navigations/{navigation} | DELETE |  |
| symbb_backend_api_site_navigation_item_save | /api/sites/{site}/navigations/{navigation}/items/{item} | POST | JSON |
| symbb_backend_api_site_navigation_item_delete | /api/sites/{site}/navigations/{navigation}/items/{item} | DELETE |  |


