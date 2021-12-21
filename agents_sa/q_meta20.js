printjson(
db.getCollection('lines').aggregate(
[
   { $group: { _id : "meta", 
       count: { $sum : 1 } ,
       minn : { $min :  '$n' },
       maxn : { $max :  '$n' },
       mintsus : { $min : '$tsus' },
       maxtsus : { $max : '$tsus' } 
      }
  }
] 
).toArray()
)
