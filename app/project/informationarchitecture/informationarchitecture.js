require(['fancytree-dnd', 'fancytree-glyph', 'fancytree-persist', 'fuelux'],function() {

  function getParentLFT(node) {
    console.log('node.getLastChild() '+node.getLastChild());
    if(node.getLastChild() == null) {
      console.log('node.getPrevSibling() '+node.getPrevSibling());
      if(node.getPrevSibling() == null) {
        console.log('node.getParent().getParent() '+node.getParent().getParent());
        if(node.getParent().getParent() == null) {
          console.log('return dummy');
          return 0;
        } else {
          console.log('return getParent()');
          return node.getParent().data.LFT;
        }
      } else {
        //return node.getPrevSibling().getLastChild();
        console.log('return recursive getPrevSibling()');
        return getParentLFT(node.getPrevSibling()); // recursive
      }
    } else {
      console.log('return getLastChild()');
      console.log(node.getLastChild().data.RGT);
      return node.getLastChild().data.RGT;
    }
  }

  function getPreviuosLFT(node) {
    if(node.getPrevSibling() == null) {
      if(node.getParent().getPrevSibling() == null) {
        if(node.getParent().getParent().getParent() == null) {
          console.log('return dummy');
          return 0;
        } else {
          console.log('return getParent().getParent()');
          return node.getParent().getParent().data.LFT;
        }
      } else {
        console.log('return getParentLFT()');
        return getParentLFT(node.getParent().getPrevSibling());
      }
    } else {
      console.log('return getPrevSibling()');
      return node.getPrevSibling().data.RGT;
    }
  }

$('.accordion').accordion({ collapsible: true });

/*
 * Pillbox for the cards
 */
$('#itemPillbox').pillbox({
  'edit': false,
  onAdd: function(data, callback) {
    $.post($('#itemPillbox').data('onaddurl'), {text:data.text}, function(result){
      //data.id = result;
      //callback(data);
      location.reload();
    });
    console.log( 'pillbox onAdd', data, callback);
  },
  onRemove: function(data, callback) {
    $.get($('#itemPillbox').data('onremoveurl')+'/'+data.id, function(){
      callback(data);
    });
    console.log( 'pillbox onRemove', data, callback);
  },
});
$('#saveCard').on('click',function(){
  var e = jQuery.Event("keydown");
  e.which = 13;
  e.keyCode = 13;
  $('.pillbox-add-item').trigger(e);
});
/*
 * Pillbox for the container
 */
$('#containerPillbox').pillbox({
  'edit': false,
  onAdd: function(data, callback) {
    console.log(data);
    console.log(callback);
  },
  onRemove: function(data, callback) {
    $.get($('#containerPillbox').data('onremoveurl')+'/'+data.id, function(){
      callback(data);
    });
    console.log( 'pillbox onRemove', data, callback);
  },
});

// make all container and cards draggable
function registerDraggable() {
  $('.draggable').draggable({
    revert: true,
    helper: "clone",
    cursorAt: { top: -5, left: -5 },
    connectToFancytree: true
  });
  console.log('register draggable');
}
registerDraggable();



    // Attach the fancytree widget to an existing <div id="tree"> element
    // and pass the tree options as an argument to the fancytree() function:
    var fancytree = $("#tree").fancytree({
      extensions: ["dnd", "persist"],
      persist: {
        expandLazy: true,
        overrideSource: false, // true: cookie takes precedence over `source` data attributes.
        store: "auto" // 'cookie', 'local': use localStore, 'session': sessionStore
      },
      // titlesTabbable: true,
      source: {
        url: $('#tree').data('url')
      },
      dnd: {
        autoExpandMS: 400,
        focusOnClick: true,
        preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
        preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
        dragStart: function(node, data) {
          /** This function MUST be defined to enable dragging for the tree.
           *  Return false to cancel dragging of node.
           */
           if(node.data.flag == 'section')
                return false;
            else
                return true;
        },
        onDrop: function(node, data, hitMode) {
        console.log(node);
        return true;
        },
        dragEnter: function(node, data) {
          /** data.otherNode may be null for non-fancytree droppables.
           *  Return false to disallow dropping on node. In this case
           *  dragOver and dragLeave are not called.
           *  Return 'over', 'before, or 'after' to force a hitMode.
           *  Return ['before', 'after'] to restrict available hitModes.
           *  Any other return value will calc the hitMode from the cursor position.
           */
          // Prevent dropping a parent below another parent (only sort
          // nodes under the same parent)
/*           if(node.parent !== data.otherNode.parent){
            return false;
          }
          // Don't allow dropping *over* a node (would create a child)
          return ["before", "after"];
*/
            if(node.data.flag == 'section')
                return ['over'];
            else
                return ['before', 'after'];
        },
        dragDrop: function(node, data) {

          var dummy = 1; // default LFT of dummy
          $.get($('#tree').data('dragdroprooturl'), function(data) {
            dummy = parseInt(data.LFT);
          });

          var el = $(data.draggable.element);

          var lft = 0; // default is dummy
          var section = 1; // default is 1

          if(data.hitMode == 'over') {
            lft = getParentLFT(node);
            section = node.data.ID;
          }
          if(data.hitMode == 'before') {
            lft = getPreviuosLFT(node);
            section = node.getParent().data.ID;
          }

          if(data.hitMode == 'after') {
            lft = node.data.RGT;
            section = node.getParent().data.ID;
          }


          lft = parseInt(lft);
          if(lft == 0)
            lft = dummy+1;
          else
            lft = lft+1;

          console.log('hitMode '+data.hitMode);
          console.log('Node: ' +node);
          console.log('OtherNode: ' +data.otherNode);
          console.log('Add within the nested set at LFT: ' +lft);

          if(!data.otherNode){
            console.log('insert node');
            // insert new item
            $.post($('#tree').data('dragdropaddurl'), {ID: el.data('id'), flag: el.data('flag'), section: section, LFT: lft}, function(result) {
              //$("#tree").fancytree("getTree").reload();
              var newNode = node.addNode(result);
              newNode.moveTo(node, data.hitMode);
              //data.otherNode.moveTo(node, data.hitMode);
              console.log('Reload Tree, otherwise add node: '+result);
              //console.log(data);
              //data.otherNode.moveTo(newNode, data.hitMode);
            });
          } else {
            console.log('move node(s)');
            console.log(data.otherNode); // What to move
            console.log(data.node); // Where to move
            // move item within the tree
            // refresh nested set!!!!!
            $.post($('#tree').data('dragdropmoveurl'), {ID: data.otherNode.data.ID, moveToLFT: lft, sectionID:section}, function(result) {
              //$("#tree").fancytree("getTree").reload();
              node.data.sectionID = section;
              data.otherNode.moveTo(node, data.hitMode);
              console.log('Move of subtree was successful');
            });
          }
        },
      },
      removeNode: function(event, data) {
        //$.post('http://bachelorarbeit.localhost/project/informationarchitecture/tree/delete/1', {ID: data.node.data.ID});
        // Optionally release resources
        console.log('Remove node: '+data.node);
        //console.log(event);

      },

      activate: function(event, data) {
//        alert("activate " + data.node);
      }
    });

  /*
   * delete selected node and every node below
   */
  $('#treeDeleteSelectedNode').click(function() {
    var node = $("#tree").fancytree("getActiveNode");

    if(node.data.flag == 'section') {
        return false;
    } else {
      $('.modalTemplate').modal({remote: $('#tree').data('removenodeurl')+'/'+node.data.ID});
    }
  });

  /*
   * add link
   */
  $('#treeAddLink').click(function() {
    var node = $("#tree").fancytree("getActiveNode");

    if(node.data.flag == 'section') {
        return false;
    } else {
      $('.modalTemplate').modal({remote: $('#tree').data('addlinkurl')+'/'+node.data.ID});
    }
  });

  $('#itemSortable, #containerSortable').sortable({
    connectWith: ".connectedSortable"
  }).disableSelection();
  $('#addContainerSubmit').click(function(){
    $.post($(this).attr('href'), $('#containerSortable').sortable('serialize') + "&containerName=" + $('#containerName').val(), function(result) {
      $('#containerPillbox').pillbox('addItems', -1,  {'text':result.name, 'value':result.ID, 'data-id':result.ID, 'data-name':result.name, 'data-flag':result.flag});
      console.log(result);
    });
      location.reload();
  });
});
