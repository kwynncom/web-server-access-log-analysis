db.getCollection('lines').aggregate(
[

	{ $group: { _id : 'tots' , filesizeInDB : { "$sum" : "$llen" } , lines : { '$sum' : 1}} }  
]
)

