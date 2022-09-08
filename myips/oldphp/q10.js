printjson(db.getCollection('usage').aggregate( // 2021/12/31 23:05
[         { $match : { 'timed.time' : { '$gte' : 1633065493   }, 'email' : {'$ne' : null} }}, 
          { $group : { _id : {  'agent' : '$agent', 'ip' : '$ip', 'email' : '$email'},
              'mints' : {'$min' : '$timed.time'},
              'maxts' : {'$max' : '$timed.time'}
               }}   ]).toArray());