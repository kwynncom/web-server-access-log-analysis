// QID-lastPtr
db.getCollection('lines').find({}).sort({'fpp1' : -1, 'fts' : -1}).limit(1);


db.getCollection('lines').aggregate(
[{ $group: { _id : '$fts' , maxfp : {'$max' : "$fpp1"}} }  ]);


db.getCollection('lines').aggregate(
[{ $group: { _id : '$fts' , filesizeInDB : { "$sum" : "$llen" } , lines : { '$sum' : 1}, maxfp : {'$max' : "$fpp1"}} }  ]);

db.getCollection('lines').find({}).sort({'fp0' : 1}).limit(1);

db.getCollection('lines').createIndex({'fts' : -1, 'fpp1' : -1});
db.getCollection('lines').dropIndex  ('fpp1_-1_fts_-1');
db.getCollection('lines').dropIndex  ('fpp1_-1');
