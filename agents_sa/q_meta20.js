printjson(
db.getCollection('lines').aggregate(
[
   {   
       $group: { _id : "meta", 
       numLines : { $sum : 1 } ,
       minn : { $min :  '$n' },
       maxn : { $max :  '$n' },
       mintsus : { $min : '$tsus' },
       maxtsus : { $max : '$tsus' } 
      }
  }
] 
).toArray()
)
