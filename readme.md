# Elasticsearch

````
PUT app_index
PUT app_index/_mapping/categories
{
  "properties": {
    "id": {
      "type": "integer"
  	},
    "title": {
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
