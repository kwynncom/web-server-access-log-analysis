function cree(type)  { return document.createElement (type); }
function byid(id  )  { return document.getElementById(id  ); }
function inht(id, s) {
    const e = byid(id);
    if (!e) return;
    e.innerHTML = s;
}