## Improvements, I recommend if it was a real project

1. Use webpack for assets instead of bower components.
2. Cover code with unit tests whenever it is possible.


## Answer for third theoretical question 

There is a lot of options to improve the plugin 
to handle millions of records. 

First of all, we'd have to restrict search by the view area 
and dynamically add it to the map. 

The second step would be to develop some kind of 
priority functionality - for example Google Maps 
shows the most important objects the lower is zoom.
I cannot say more details as it suppose to depend  
on the business requirements for the plugin.

Then i would discover more options for optimization queries.
There is an extension for MySql databases for coordinates and 
geolocation. Beside of that i'd dive into ElasticSearch 
to get known if it can handle geo-data and could improve query performance. (I used a
plugin for text search in WordPress with ElasticSearch once - speed was increased significantly).

In there is an option to develop pins functionality as 
independent microservice with another framework.

That is the first ideas i have on this - my actual 
decision would depend on the real world conditions and requirements. 
