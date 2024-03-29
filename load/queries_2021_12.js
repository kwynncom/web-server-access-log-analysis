// mongo wsal --quiet q1.js

printjson(db.getCollection('lines').aggregate(
[
   { $group: { _id : "$agent", count: { $sum: 1} } },
   { $sort : {'count' : -1}}
] 
));

// results in _batch variable


db.getCollection('lines').createIndex({'agent' : 1})

db.getCollection('lines').aggregate(
[
   { $group: { _id : "meta", 
       count: { $sum : 1 } ,
       minn : { $min :  '$linen' },
       maxn : { $max :  '$linen' },
       mints : { $min : '$tsus' },
       maxts : { $max : '$tsus' } 
      }
  }
] 
)
// ******
db.getCollection('lines').aggregate(
[
   { $group: { _id : "counts", 
       count: { $sum: 1} } 
      }
   
] 
)
// ***********
db.getCollection('lines').aggregate(
[
   { $group: { _id : "$agent", count: { $sum: 1} } },
   { $sort : {'count' : -1}}
] 
)
// *************
db.getCollection('lines').aggregate(
[
   { $group: { _id : "$agent", count: { $sum: 1} } },
   { $sort : {'count' : -1}},
   { $match: {'_id' : /\+https?:\/\//} }
] 
)
