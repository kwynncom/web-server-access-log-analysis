db.getCollection('lines').aggregate(
[
{ $match: {'ftsl1' : 1644461682}},
{ $group: { '_id' : 'maxfp', maxfp : {'$max' : "$fpp1"}} }
]);

db.getCollection('lines').aggregate(
[{ $group: { _id : '$ftsl1' , filesizeInDB : { "$sum" : "$len" } , lines : { '$sum' : 1}, maxfp : {'$max' : "$fpp1"}} }  ]);

db.getCollection('lines').createIndex({'ftsl1' : -1, 'fpp1' : -1}, {'unique' : true})

// QID-lastPtr
db.getCollection('lines').find({}).sort({'fpp1' : -1, 'fts' : -1}).limit(1);


db.getCollection('lines').find({}).sort({'fpp1' : 1 }).limit(95134).forEach(function(r) {
    print(r.line.trim());
});

db.getCollection('lines').count()
// 95134

// mongo wsal --quiet -eval "db.getCollection('lines').find({}).sort({'fpp1' : 1}).limit(95134).forEach(function(r) { print(r.line.trim()); });" | openssl md5


/* head -n 86260 access.log | openssl md5
(stdin)= 067f46711ae9887d67ab64285aaab34a 
mongo wsal --quiet -eval "db.getCollection('lines').find({}).sort({'fts' : 1, 'fpp1' : 1}).limit(86260).forEach(function(r) { print(r.line.trim()); });" | openssl md5
(stdin)= 067f46711ae9887d67ab64285aaab34a
*/

/*
mongo wsal --quiet -eval "db.getCollection('lines').find({}).sort({'fts' : 1, 'fpp1' : 1}).limit(93035).forEach(function(r) { print(r.line.trim()); });"
*/
db.getCollection('lines').aggregate(
[{ $group: { _id : '$fts' , maxfp : {'$max' : "$fpp1"}} }  ]);


db.getCollection('lines').aggregate(
[{ $group: { _id : '$fts' , filesizeInDB : { "$sum" : "$llen" } , lines : { '$sum' : 1}, maxfp : {'$max' : "$fpp1"}} }  ]);

db.getCollection('lines').find({}).sort({'fp0' : 1}).limit(1);

db.getCollection('lines').createIndex({'fts' : -1, 'fpp1' : -1});
db.getCollection('lines').dropIndex  ('fpp1_-1_fts_-1');
db.getCollection('lines').dropIndex  ('fpp1_-1');
