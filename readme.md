# Elasticsearch

````
PUT app_index
PUT app_index/_mapping/categories
{
  "properties": {
    "id": {
      "type": "integer"
  	},
    "name": {
  	  "type": "string"
    },
	  "description": {
  	  "type": "text"
  	},  
	 "content": {
  	  "type": "text"
  	 }
  }
}
````
