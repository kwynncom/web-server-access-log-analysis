printjson(
db.getCollection('lines').aggregate(
[
   { $group: { _id : "$agent", count: { $sum: 1} } },
   { $sort : {'count' : -1}}
] 
).toArray()
)
