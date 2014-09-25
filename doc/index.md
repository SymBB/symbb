Getting Started With Symbb
==========================

# Routes

## Frontend Api Routes

## Backend Api Routes

### Sites

| Name | Pattern | POST Body | Result |
| ------------- | ----------- | ----------- | ----------- |
| symbb_backend_api_site_list | /api/sites | GET | JSON |
| symbb_backend_api_site_save | /api/sites/{id} | POST | JSON |
| symbb_backend_api_site_data | /api/sites/{id} | GET | JSON |
| symbb_backend_api_site_delete | /api/sites/{id} | DELETE | JSON |
| symbb_backend_api_site_navigation_list | /api/sites/{id}/navigations | GET | JSON |
| symbb_backend_api_site_navigation_save | /api/sites/{id}/navigations/{navigation} | POST | JSON |
| symbb_backend_api_site_navigation_delete | /api/sites/{id}/navigations/{navigation} | DELETE | JSON |
| symbb_backend_api_site_navigation_item_save | /api/sites/{id}/navigations/{navigation}/items/{item} | POST | JSON |
| symbb_backend_api_site_navigation_item_delete | /api/sites/{id}/navigations/{navigation}/items/{item} | DELETE | JSON |
