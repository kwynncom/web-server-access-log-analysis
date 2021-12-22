printjson(
db.getCollection('lines_ua20').aggregate(
[
   { $group: { _id : "$agent", count: { $sum: 1} } },
   { $sort : {'count' : -1}}
] 
).toArray()
)

