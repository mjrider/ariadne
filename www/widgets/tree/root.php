<?php
  /******************************************************
  arguments: $path, $function="view.html", $args="", 
             $layout -> different frame layouts.
                at least three frames defined: treeview, treeload, view
                onOpen will call link+"tree.load.html?$args" in treeload
                normal click will call link+"$function?$args" in view

  call GetFolder.html onOpen with $args

  

  *******************************************************

  start met $path geopend

  FIXME: node toevoegen moet op basis van link gaan, dus elke node met die
         link moet de nieuwe node als child krijgen.

  ******************************************************/
?>
<html>
<head>
<title>Tree: <?php echo $path; ?></title>
<script language="javascript">
<!--

  Nodes=new Array();
  Links=new Array();
  id=1;
  root=0;
  target=0;
  caller=0;
  ShowInvis=false;
  NodeOpened=false;

  function AddLinks(parent, icon, name, link, pre) {
    if (Links[parent]) {
      for (i=0; i<Links[parent].length; i++) {
        if (Links[parent][i].status=="Open" || Links[parent][i].firstChild) {
          Links[parent][i].add(icon, name, link, pre);
        }
      }
    }
    Draw();
  }

  function UpdateLinks(icon, name, link, pre) {
    if (Links[link]) {
      for (i=0; i<Links[link].length; i++) {
        Links[link][i].set(icon, name, link, pre);
      }
    }
    Draw();
  }

  function DelLinks(link) {
    if (Links[link]) {
      for (i=0; i<Links[link].length; i++) {
        Links[link][i].del();
      }
      Draw();
    }
  }

  function Node(parent, prev, next, icon, name, link, pre) {
    this.id=id++;
    this.parent=parent;
    this.prev=prev;
    this.next=next;
    this.name=name;
    if (this.name.length>20) {
      this.name=this.name.substring(0,20)+"...";
    }
    this.pre=pre;
    this.link=link;
    this.icon=icon;
    this.status="Closed";
	this.visible=true;
	this.editable=true;
    this.children=new Array;
    this.add=addNode;
    this.set=setNode;
    this.del=delNode;
    this.draw=drawNode;
    this.order=orderNode;
    Nodes[this.id]=this;
    if (!Links[this.link]) {
      Links[this.link]=new Array();
    }
    Links[this.link][Links[this.link].length]=this;
  }

  function setNode(icon, name, link, pre) {
    this.icon=icon;
    this.name=name;
    this.link=link;
    this.pre=pre;
    this.order();
  }

  function delNode() { 
    if (this.prev) {
      this.prev.next=this.next;
    } else if (this.parent) {
      this.parent.firstChild=this.next;
    }
    if (this.next) {
      this.next.prev=this.prev;
    }
    // free memory someway?
  }

  function addNode(icon, name, link, pre) {
    if (this.children[link]) { // this node already exists
      if (this.children[link].name!=name) { // name changed, so reorder.
        this.children[link].del(); // first remove
        this.add(icon, name, link, pre); // then add again
      } else {
        this.children[link].set(icon, name, link);
      }   
    } else if (this.firstChild) {
      node=this.firstChild;
      while ((node.name<name) && node.next) {
        node=node.next;
      }
      if (node.name>=name) { // insert new object before node
        if (node.name==name && node.link==link && node.icon==icon) {
	  // do nothing, node is exactly the same as existing
        } else {
          temp=node.prev
          node.prev=new Node(node.parent, temp, node, icon, name, link, pre);
          if (temp) {
            temp.next=node.prev;
          } else {
            node.parent.firstChild=node.prev;
          }
        }
      } else { // new object last in list.
        node.next=new Node(node.parent, node, 0, icon, name, link, pre);
      }
    } else { // first object, no need to compare         
      this.firstChild=new Node(this, 0, 0, icon, name, link, pre);
    }
  }

  function orderNode() {
    // before reordering, first cut this node from the list
    if (this.prev) {
      this.prev.next=this.next;
    } else {
      this.parent.firstChild=this.next;
    }
    if (this.next) {
      this.next.prev=this.prev;
    }
    if (this.parent && this.parent.firstChild) {
      // now start at the first node and search till a node has a 'bigger' name
      node=this.parent.firstChild;
      while ((node.name<this.name) && node.next) {
        node=node.next;
      }
      if (node.name>=this.name) { // insert new object before node
        // reinsert this before 'node'
        // first fix pointers from 'this'
        // then fix pointers from prev or parent to this
        this.next=node;
        if (node.prev) {
          this.prev=node.prev;
          this.prev.next=this;
        } else {
          this.parent.firstChild=this;
        }
        // finally fix pointers from next to this
        node.prev=this;
      } else { // new object last in list.
        node.next=this;
        this.next=0;
        this.prev=node;
      }
    } else {
      this.parent.firstChild=this;
    }
  }

function drawNode(pre, level) {
	// display node with its style	
	// + - both displayed, one in hidden div, other visible
	// onClick -> switch div and display/hide children
    result='';
	addpre='';
	imgplus='plus';
	imgminus='minus';
	imgjoin='join';
	img='';
	if (this.visible || ShowInvis) {
		style='';
		post='';
		if (this.editable) {
			style+='<span class="editable">';
		} else {
			style+='<span class="fixed">';
		}
		post+='</span>';
		next=this.next;
		while (next && !next.visible && !ShowInvis) {
			next=next.next;
		} 
		prev=this.prev;
		while (prev && !prev.visible && !ShowInvis) {
			prev=prev.prev;
		}
		if (next) {
			addpre='<img src="../../images/tree/line.gif" alt="" width=18" height="18" align="left" valign="middle">'
			if (!prev && pre=='') {
				img+='top';
			}
		} else {
			addpre='<img src="../../images/tree/blank.gif" alt="" width=18" height="18" align="left" valign="middle">'
			if (!prev && pre=='') {
				img+='only';
			} else {
				img+='bottom';
			}
		}
		if (!this.visible) {
			style+='<span class="invisible">';
			post+='</span>';
		}
		if (this.status=="Open") {
			currimg='minus';
			plusminus='<a href="javascript:parent.toggle(\''+this.id+'\');"><img src="../../images/tree/minus'+img+'.gif" alt="" width=18" height="18" border="0" align="left" valign="middle"></a>';
		} else {
			currimg='plus';
			plusminus='<a href="javascript:parent.toggle(\''+this.id+'\');"><img src="../../images/tree/plus'+img+'.gif" alt="" width=18" height="18" border="0" align="left" valign="middle"></a>';
		}
		if (this.icon) {
			icon='<img src="../../images/icons/'+this.icon+'.gif" alt="" width="18" height="18" border="0" align="left" valign="middle">';
		} else {
			icon='';
		}
		width=(level*18)+125;
		// Mozilla ignores <nobr> tags with image placement, so calculate a minimum width here
		result='<div id="'+this.id+'" class="node"><div class="row" style="width: '+width+'px;"><nobr>'+
			pre+plusminus+'<a href="javascript:parent.View(\''+this.id+'\');">'+
            icon+'<span class="item">'+style+this.name+post+'</span></a></nobr></div>';
		if (this.firstChild && this.status=="Open" ) {
			result=result+'<div name="sub" class="submenu" id="'+this.id+'_submenu">';
			result=result+this.firstChild.draw(pre+addpre, level+1);
			result=result+'</div>';
		}
	} 
	result=result+'</div>';
	if (this.next) {
		result=result+this.next.draw(pre, level);
 	}
    return result;
}

function Draw() {
	target=window.treeview;
	if (target.document.body && ( target.document.body.scrollTop || target.document.body.scrollLeft ) ) {
		y=target.document.body.scrollTop;
		x=target.document.body.scrollLeft;
	} else {
		x=target.pageXOffset;
		y=target.pageYOffset;
	}
	// Draw the entire tree
	target.document.open();
    MenuDraw="<html>\n<head>\n<BASE HREF='"+document.location.href+"'>\n<link REL=STYLESHEET type='text/css' HREF='../../styles/tree.css'>\n";
    MenuDraw=MenuDraw+"</head>\n<body>\n";
	MenuDraw=MenuDraw+root.draw('',1);
	MenuDraw=MenuDraw+"</body>\n</html>";
	target.document.writeln(MenuDraw);
	target.document.close();
	if (x || y) {
		if (x && NodeOpened) {
			x+=75;
		}
		if (y && NodeOpened) {
			y+=75;
		}
		NodeOpened=false;
		target.scrollTo(x,y);
	}
	parent.LoadingDone();
}

  function toggle(id) {
    if (Nodes[id]) {
      node=Nodes[id];
      if (node.status=="Closed") {
        node.status="Open";
		NodeOpened=true;
        if (!node.firstChild) {
          treeload.document.location='<?php echo $loader; ?>'+node.link+'tree.load.phtml?node='+id;
        } else {
          Draw();
        }
      } else {
        node.status="Closed";
        Draw();
      }
    } else {
      count=Nodes.length;
      msg='';
      for (i=0; i<count; i++) {
        msg+='id: '+i+' value: '+Nodes[i]+'\n';
      }
      Draw();
    }
  }

  function View(id) {
	// this call will break-up konqueror
	//    window.parent.Loading(); 
    window.parent.View(Nodes[id].link);
  }

  function Open(id) {
    window.parent.Loading();
    window.parent.Open(Nodes[id].link);
  }

  function init(icon, name, path, pre) {
    Nodes=new Array();
    Links=new Array();
    id=1;

    root=new Node(0, 0, 0, icon, name, path, pre);
  }

// -->
</script>
<?php
  if (!$layout) {
    $layout="./frames.js";
  } else {
    $layout=ereg_replace("[\./\\]","",$layout).".js";
  }
  include($layout);
?>
</html>