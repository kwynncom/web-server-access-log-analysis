printjson(
db.getCollection('lines_ua20').aggregate(
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
