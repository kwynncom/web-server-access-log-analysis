printjson(
db.getCollection('lines').aggregate(
[
   {   
       $group: { _id : "meta", 
       numLines : { $sum : 1 } ,
       minn : { $min :  '$n' },
       maxn : { $max :  '$n' },
       mints : { $min : '$ts' },
       maxts : { $max : '$ts' } 
      }
  }
] 
).toArray()
)
