// Threaded Menu - version 1.1.0
// Henrik Gemal - http://gemal.dk/util/

function getId(id) {
  if (typeof(document.getElementById) != "undefined") {
    return document.getElementById(id);
  } else {
    return document.all[id];
  }
}

function thread(node,id) {
  var divid = getId(id);
  if (divid.style.display == 'none') {
    if (node.childNodes && node.childNodes.length > 0)
      if (node.childNodes.item(0).nodeName == "IMG") {
        node.childNodes.item(0).src = "images/arrow2.gif";
        node.childNodes.item(0).alt = "收缩";
        node.title = "收缩";
      }
    divid.style.display = 'block';
  } else {
    if (node.childNodes && node.childNodes.length > 0)
      if (node.childNodes.item(0).nodeName == "IMG") {
        node.childNodes.item(0).src = "images/arrow1.gif";
        node.childNodes.item(0).alt = "展开";
        node.title = "展开";
      }
    divid.style.display = 'none';
  }
}
