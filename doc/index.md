Getting Started With Symbb
==========================


# Api

## Frontend Api

### Frontend Api Routes

## Backend Api

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
| symbb_backend_api_site_list | /api/sites | GET | --- |
| symbb_backend_api_site_save | /api/sites/{id} | POST | JSON |
| symbb_backend_api_site_data | /api/sites/{id} | GET | --- |
| symbb_backend_api_site_delete | /api/sites/{id} | DELETE | --- |
| symbb_backend_api_site_navigation_list | /api/sites/{id}/navigations | GET | --- |
| symbb_backend_api_site_navigation_save | /api/sites/{id}/navigations/{navigation} | POST | JSON |
| symbb_backend_api_site_navigation_delete | /api/sites/{id}/navigations/{navigation} | DELETE | --- |
| symbb_backend_api_site_navigation_item_save | /api/sites/{id}/navigations/{navigation}/items/{item} | POST | JSON |
| symbb_backend_api_site_navigation_item_delete | /api/sites/{id}/navigations/{navigation}/items/{item} | DELETE | --- |


