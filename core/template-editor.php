<?php

// special monaco editor loader...
$monacoLoader = $_REQUEST["monaco-editor-worker-loader"] ?? "";
if ($monacoLoader == "proxy.js")
{
    $monacoVersion = "0.13.1";

    echo 
<<<CODEJS
self.MonacoEnvironment = {
	baseUrl: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/$monacoVersion/min/'
};
importScripts('https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/$monacoVersion/min/vs/base/worker/workerMain.js');
CODEJS;

    exit;    
}

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?php O::O("Editor") ?></title>
        
        <link rel="icon" type="image/jpeg" href="media/img/256x256-logo.jpg" />
        
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <link rel="stylesheet" href="media/css/site.css" type="text/css">
        
    </head>
    <body>
        <div class="page">
            
            <header>
                <nav>
                    <ul>
                        <li><a class="act-reload" href="#act-reload">ONE</a>
                            <ul>
                                <li v-show="ajaxSession.length < 10">
                                    <form @submit.prevent="actLogin">
                                        <input type="text" name="email" placeholder="email">
                                        <input type="password" name="password" placeholder="password">
                                        <button type="submit">login</button>
                                    </form>
                                    <hr>
                                    <b>{{ ajaxSession }}</b>
                                </li>
                                <li v-show="ajaxSession.length > 10">
                                    <form @submit.prevent="actLogout">
                                        <button type="submit">logout</button>
                                    </form>
                                </li>
                                <li><h6>WIDTH</h6>
                                    <input type="range" min="0" max="100" step="1" v-model="range1" :title="'EXPLORER ' + range1" @change="resizeEditor">
                                    <input type="range" min="0" max="100" step="1" v-model="range2" :title="'OPEN ' + range2" @change="resizeEditor">
                                    <input type="range" min="0" max="100" step="1" v-model="range3" :title="'CODE ' + range3" @change="resizeEditor">
                                    <input type="range" min="0" max="100" step="1" v-model="range4" :title="'HELPER ' + range4" @change="resizeEditor">
                                </li>
                                <li><h6>ORDER</h6>
                                    <input type="range" min="1" max="4" step="1" v-model="order1" :title="'EXPLORER ' + order1" @change="resizeEditor">
                                    <input type="range" min="1" max="4" step="1" v-model="order2" :title="'OPEN ' + order2" @change="resizeEditor">
                                    <input type="range" min="1" max="4" step="1" v-model="order3" :title="'CODE ' + order3" @change="resizeEditor">
                                    <input type="range" min="1" max="4" step="1" v-model="order4" :title="'HELPER ' + order4" @change="resizeEditor">
                                </li>
                            </ul>
                        </li>
                        <li v-show="ajaxSession.length > 10"><a href="#crud">crud</a>
                            <ul>
                                <li><button class="act-crud-table" data-filename="">manage SQL tables</button></li>
                                <li><button class="act-crud-line" data-filename="">manage SQL content</button></li>
                            </ul>
                        </li>
                        <li v-show="ajaxSession.length > 10"><a href="#act-run-script">run script</a>
                            <ul>
                                <li><button class="act-run2" data-filename="/git-auto.sh">/git-auto.sh</button></li>
                                <li><button class="act-run2" data-filename="/synchro.php">/synchro.php</button></li>
                                <li><button class="act-run2" data-filename="/synchro-zip-plugin.php">/synchro-zip-plugin.php</button></li>
                                <li><button class="act-run2" data-filename="/synchro-zip-theme.php">/synchro-zip-theme.php</button></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">ReCoder</a>
                            <ul>
                                <li><div>GROUP</div><input type="text" v-model.trim="recordSession"></li>
                                <li><div>REPLAY</div>
                                    <input type="radio" value="replay" v-on:change="switchSynchro" v-model="recordSynchro">
                                    <input type="number" min="400" step="200" v-on:change="switchSynchro" v-model="recordReplayMs"><span> ms</span>
                                </li>
                                <li v-show="ajaxSession.length > 10"><div>RECORD</div><input type="radio" value="record" v-on:change="switchSynchro" v-model="recordSynchro"></li>
                                <li><div>off</div><input type="radio" value="" v-on:change="switchSynchro" v-model="recordSynchro"></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">open files</a>
                            <ul>
                                <li>
                                    <button class="actResetOpen">OPEN FILES RESET</button>
                                </li>
                                <li><h6>max files ({{ openMax }})</h6>
                                    <input type="range" min="1" max="100" step="1" v-model="openMax" :title="'MAX OPEN FILES ' + openMax">
                                </li>
                            </ul>
                        </li>
                        <li v-show="ajaxSession.length > 10">
                            <a href="#act-options">dir/file</a>
                            <ul>
                                <li>path
                                    <h5 class="file-target" contentEditable="true">{{currentFile}}</h5>
                                </li>
                                <li>file
                                    <br>
                                    <button class="actFile actFileCreate" value="actFileCreate">new</button>
                                    <button class="actFile actFileRename" value="actFileRename">rename</button>
                                    <button class="actFile actFileDelete" value="actFileDelete">delete</button>
                                    <hr>
                                </li>
                                <li>dir
                                    <h5 class="dir-target" contentEditable="true">{{currentDir}}</h5>
                                    <br>
                                    <button class="actFile actDirCreate" value="actDirCreate">new</button>
                                    <button class="actFile actFileRename" value="actFileRename">rename</button>
                                    <button class="actFile actDirDelete" value="actDirDelete">delete</button>
                                    <hr>
                                </li>
                                <li>
                                    <form v-on:submit.prevent="addNewTodo">
                                        <label for="new-todo">add folder/file</label>
                                        <input
                                        v-model="newTodoText"
                                        id="new-todo"
                                        placeholder="exemple: travailler sur vuejs"
                                        >
                                        <button>add</button>
                                    </form>

                                </li>
                                <li><div>
                                    <label for="choice1">choix1</label><input id="choice1" type="radio" name="choiceRun">
                                </div></li>
                                <li><div>
                                    <label for="choice2">choix1</label><input id="choice2" type="radio" name="choiceRun">
                                </div></li>
                                <li><div>
                                    <label for="choice3">choix1</label><input id="choice3" type="radio" name="choiceRun">
                                </div></li>
                                <li><div>
                                    <label for="choice4">choix1</label><input id="choice4" type="radio" name="choiceRun">
                                </div></li>
                            </ul>

                        </li>
                        <li>
                            <pre v-show="ajaxMessage.length > 2" @click.prevent="ajaxMessage = ''" class="ajaxMessage">{{ ajaxMessage }}</pre><small>{{ currentFile }}</small> 
                        </li>
                        <li>
                            <div v-show="editorSelection.length < 160">...{{ editorSelection}}...</div>
                            <ul v-show="ajaxSession.length > 10">
                                <li>
                                    <h6>FILE</h6>
                                    <button class="act-run">run</button>
                                    <button class="act-save">save</button>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </header>

            <main>

<?php
$tabData        = [];
$tabDataFile    = [];

$rootDir0 = Core::get("rootdir0");

$tabSearchDir   = [];
$tabSearchDir[] = "";

$explorer = Core::Explorer()->buildList($tabSearchDir);

?>

            <section class="explorer-zone" v-bind:style="{ width: range1 + 'vw', order: order1 }">
<div id="todo-list-example">
    <!-- Explorer -->
    <div class="todo-list">
        <transition-group
            name="update-event"
            tag="ol"
            v-bind:css="false"
            v-on:enter="enterList"
          >
            <li
        is="todo-item"
        v-for="(todo, index) in todos"
        v-bind:key="todo.id"
        v-bind:title="todo.title"
        v-bind:isdir="todo.isdir"
        v-bind:dirname="todo.dirname"
        v-bind:basename="todo.basename"
        v-bind:extension="todo.extension"
        v-bind:level="todo.level"
        v-on:remove="todos.splice(index, 1)"
            ></li>
        </transition-group>

    </div>
</div>
            <div class="menuBox"></div>
            </section>

            <section class="toolbar-zone" v-bind:style="{ width: range2 + 'vw', order: order2 }">
                <!-- Toolbar -->
                <div class="toolbar-box">
                    <template v-for="openF in tabOpen">
                        <div class="recentFile" @click="reOpen" v-html="openF"></div>
                    </template>
                    <div>
                        <h4>emmet code</h4>
                        <textarea class="emmetCode" cols="80" rows="10">{{lineCode}}</textarea>
                    </div>
                </div>
            </section>
            
            <section class="editor-zone" v-bind:style="{ width: range3 + 'vw', order: order3 }">
                <!-- Editor -->
                <div class="editor-box"></div>
            </section>

            <section class="help-zone" v-bind:style="{ width: range4 + 'vw', order: order4 }">
                <!-- Toolbar -->
                <div class="help-box">
                    <div>
                        <button @click="helpGoUrl">GO</button>
                        <input type="checkbox" v-model="saveRefresh" title="refresh after save ;-p">
                    </div>    
                    <input type="text" v-model.lazy="helpUrl">
                    <!--
                        <iframe class="helpFrame" src="" @load="updateHelpUrl" frameborder="0"></iframe>
                    -->    
                </div>
            </section>

            </main>
            <footer>

                <?php // $objView->afficherMenu("footer", "<nav><ul>", "</ul></nav>") ?>
            </footer>

            <div class="lightbox">
                <form class="ajax">
                    <div v-for="wl in tabWindow" track-by="title">
                        <div v-if="wl.html != 'button'">
                            {{ wl.title }}
                            <input v-if="wl.html == 'text'" type="text" :name="wl.name">
                            <input v-if="wl.html == 'password'" type="password" :name="wl.name">
                            <textarea v-if="wl.html == 'textarea'" :name="wl.name"></textarea>
                        </div>
                        <div v-if="wl.html == 'button'">
                            <button type="submit" :name="wl.name">{{ wl.title }}</button>
                        </div>
                    </div>
                </form>
                <div class="window" v-html="codeWindow"></div>
            </div>

        </div><!--/page -->
<!--
                <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js">
                </script>
-->
                <script src="media/js/vue.js"></script>
                </script>
                <script>
var Explorer = {};


Explorer.find = function (selector)
{
    return document.querySelector(selector);
}
Explorer.findAll = function (selector)
{
    return document.querySelectorAll(selector);
}
Explorer.on = function (selector, action, actionCB)
{
    var tabEl = Explorer.findAll(selector);
    if(tabEl !== undefined) {
        for(el of tabEl) {
            if (el !== undefined) el.addEventListener(action, actionCB);
        }
    }
}

Explorer.val = function (selector) {
    var el = Explorer.find(selector);
    if (el !== undefined && el.value !== undefined) return el.value;
    else return '';
}

Explorer.doCss = function (objParam)
{
    if (objParam.sel !== undefined)
    {
        var el = Explorer.find(objParam.sel);
        if (el !== undefined) 
        {
            if (objParam.add !== undefined)
            {
                el.classList.add(objParam.add);
            }
            if (objParam.remove !== undefined)
            {
                el.classList.remove(objParam.remove);
            }
        }
        
    }
}
Explorer.hide = function (selector)
{
    var el = Explorer.find(selector);
    if (el !== undefined) el.classList.remove("show");
}

Explorer.tabSearchDir = [];
Explorer.ajaxData     = {};
Explorer.ajaxLoad = function ()
{
    var urlAjax = "ajax";
    var form = new FormData();
    form.append("--formGoal",   "Explorer");
    // add session info
    form.append("ajaxSession",  Explorer.vue.ajaxSession);

    if (arguments.length == 1) {
        Explorer.ajaxAction      = "read";
        filename = arguments[0];
        Explorer.vue.currentFile = filename;
        form.append("filename",     filename);
    }
    if (arguments.length == 2) {
        form.append("filename",     Explorer.vue.currentFile);

        Explorer.ajaxAction   = arguments[0];
        code                  = arguments[1];
        if (typeof code !== "object")
        {
            form.append("code",         code);
        }
        else
        {
            form.append("json",         JSON.stringify(code));
        }
    }
    if (arguments.length == 3) {
        Explorer.ajaxAction     = arguments[0];
        filename                = arguments[1];
        
        // refresh list files
        jsonParam               = {};
        jsonParam.method        = "listFile";
        jsonParam.tabSearchDir  = Explorer.tabSearchDir;

        form.append("json",         JSON.stringify(jsonParam));
        
        if (Explorer.ajaxAction == "upload")
        {
            upload                  = arguments[2];
            form.append("filename", filename);
            form.append("upload",   upload);
        }

        if (Explorer.ajaxAction == "actDirCreate")
        {
            dirCreate               = arguments[1];
            filename                = arguments[2];
            form.append("dirCreate",    dirCreate);
            form.append("filename",     filename);
        }
        if (Explorer.ajaxAction == "actFileCreate")
        {
            fileCreate              = arguments[1];
            filename                = arguments[2];
            form.append("fileCreate",   fileCreate);
            form.append("filename",     filename);
        }
        if (Explorer.ajaxAction == "actFileDelete")
        {
            fileDelete              = arguments[1];
            filename                = arguments[2];
            form.append("fileDelete",   fileDelete);
            form.append("filename",     filename);
        }
        if (Explorer.ajaxAction == "actDirDelete")
        {
            dirDelete               = arguments[1];
            filename                = arguments[2];
            form.append("dirDelete",    dirDelete);
            form.append("filename",     filename);
        }
        if (Explorer.ajaxAction == "actFileRename")
        {
            fileRename              = arguments[1];
            filename                = arguments[2];
            form.append("fileRename",   fileRename);
            form.append("filename",     filename);
        }
    }
    
    form.append("action",       Explorer.ajaxAction);
    // waiting
    Explorer.vue.ajaxMessage = "...";

    fetch(urlAjax, {
        method: "POST",
        credentials: 'include', // SEND ALSO COOKIES FOR SESSION...
    	body: form
	})
	.then(function(response) {
	    //console.log(response);
	    return response.json();
	})
	.then(function(json) {
	    //console.log(json);
	    if (json.ajaxReplay !== undefined) {
            Explorer.updatePositionActive = false;
	        // update the model
            var recordFilename  = json.ajaxReplay.recordFilename;
            var recordLine      = parseInt(json.ajaxReplay.recordLine);
            var recordCode      = atob(json.ajaxReplay.recordCode);
            var recordSelection = atob(json.ajaxReplay.recordSelection);
            
            Explorer.vue.editorSelection = recordSelection;
            
            Explorer.vue.currentFile = recordFilename;
            Explorer.updateOpen(recordFilename);
            if (recordFilename == Explorer.vue.currentFile)
            {
                //var curLN = parseInt(localStorage.getItem(Explorer.vue.currentFile));
                var curLN = Explorer.editor.getPosition().lineNumber;
                if (recordCode != Explorer.editor.getValue())
                {
                    Explorer.editorModel.setValue(recordCode);
                }
                
                if (curLN != recordLine)
                {
                    Explorer.editor.revealLineInCenter(recordLine, 0);
                    Explorer.editor.setPosition({column: 1, lineNumber: recordLine});                
                    localStorage.setItem(Explorer.vue.currentFile, recordLine);
                    Explorer.vue.ajaxMessage = 'synchro:' + recordLine + ':' + curLN;
                }
            }
            Explorer.updatePositionActive = true;
	    }
	    if (json.ajaxListFile !== undefined) {
	        // update the model
	        Explorer.vue.todos = json.ajaxListFile;
	        var listOpen = document.querySelectorAll(".todo-list > .open");
	    }
	    if (json.tabWindow !== undefined) {
	        // update the model
	        Explorer.vue.tabWindow = json.tabWindow;
	    }
	    if (json.codeWindow !== undefined) {
	        // update the model
	        Explorer.vue.codeWindow = json.codeWindow;
	    }
	    if (json.ajaxMessage !== undefined) {
	        Explorer.vue.ajaxMessage = json.ajaxMessage;
	    }
	    if (json.ajaxUrlReload !== undefined) {
            // reload helpFrame if useful
            // console.log(Explorer.helpFrame.src);
            if (Explorer.vue.saveRefresh || (Explorer.helpFrame.src.indexOf(Explorer.vue.currentFile) >= 0))
            {
                try {
                    Explorer.helpFrame.contentWindow.location.reload(true);
                }
                catch(error) {
                    console.log(error);
                }
            }
	    }
	    if (json.ajaxSession !== undefined) {
	        Explorer.vue.ajaxSession = json.ajaxSession;
	    }
	    if (json.filecontent !== undefined) {
            // go back to last focus line
            if (Explorer.vue.currentFile) 
            {
                var curLN = localStorage.getItem(Explorer.vue.currentFile);
            }
            // UPDATE CODE IN EDITOR
	        Explorer.editorModel.setValue(json.filecontent);
            // KEEP EXTENSION
            if (json.codeLanguage !== undefined) {
                localStorage.setItem('ext@' + Explorer.vue.currentFile, json.codeLanguage);
    	        monaco.editor.setModelLanguage(Explorer.editorModel, json.codeLanguage);
            }

            if (curLN) {
                curLN = parseInt(curLN);
                Explorer.editor.revealLineInCenter(curLN);
                Explorer.editor.setPosition({column: 1, lineNumber: curLN});
                // console.log(curLN+':'+Explorer.vue.currentFile);
            }
	    }
	    if (json.codeLanguage !== undefined) {
	        // https://microsoft.github.io/monaco-editor/api/modules/monaco.editor.html#setmodellanguage
	        monaco.editor.setModelLanguage(Explorer.editorModel, json.codeLanguage);
	    }
	    else if (json.fileextension !== undefined) {
	        // https://microsoft.github.io/monaco-editor/api/modules/monaco.editor.html#setmodellanguage
	        monaco.editor.setModelLanguage(Explorer.editorModel, json.fileextension);
	    }
	});
			    
}

Explorer.preventDefaults = function (e) {
  e.preventDefault();
  e.stopPropagation();
}

// https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
Explorer.actDrop = function (event)
{
    Explorer.dropFiles = event.dataTransfer.files;
    var info = '';
    [...Explorer.dropFiles].forEach(dfile => {
        // console.log(dfile);
        info += ' + ' + dfile.name + ' / '+ parseInt(dfile.size / 1024) + 'Ko \n';
        // upload file
        Explorer.ajaxLoad("upload", this.getAttribute('filename'), dfile);
    });
    Explorer.vue.ajaxMessage = '(drop ' + this.getAttribute('filename') + ')\n' + info;

    console.log(Explorer.dropFiles);
    
};

Explorer.actDragEnter = function (event)
{
    Explorer.vue.ajaxMessage = '(drop ' + this.getAttribute('filename') + ')';
};

Explorer.updateCode = '';
Explorer.updatePositionActive = true;
Explorer.updatePositionEditor = function(e)
{
    // fixme: to remove?
    if (!Explorer.updatePositionActive) return;

    // don't update if replay mode
    if (Explorer.vue.recordSynchro == 'replay') return;

    var curLN = parseInt(e.position.lineNumber);
    var curCode = Explorer.editor.getValue();
    if ((curLN != Explorer.lineNumber) || (curCode != Explorer.updateCode))
    {
        Explorer.lineNumber = curLN;
        Explorer.updateCode = curCode;

        // console.log(Explorer.lineNumber + ',' + Explorer.vue.currentFile);
        localStorage.setItem(Explorer.vue.currentFile, Explorer.lineNumber);
        // send current position for synchro
        if (Explorer.vue.recordSynchro == 'record')
        {
            // console.log(Explorer.updateCode);
            Explorer.ajaxLoad("json", { 
                    method: "record", 
                    recordSession:      Explorer.vue.recordSession, 
                    recordFilename:     Explorer.vue.currentFile, 
                    recordLine:         Explorer.lineNumber,
                    // btoa to neutralize code...
                    recordSelection:    btoa(Explorer.vue.editorSelection),
                    recordCode:         btoa(Explorer.updateCode) 
                });
        }
    }
}

// SELECTION
Explorer.editorSelectionEvent = null;
Explorer.updateSelectionEditor = function(e)
{
    if (!Explorer.updatePositionActive) return;

    Explorer.vue.editorSelection = Explorer.editor.getModel().getValueInRange(Explorer.editor.getSelection())
    Explorer.editorSelectionEvent = e;
    //console.log(Explorer.editorSelection);
    //console.log(e);
    //if (Explorer.editorSelection.length > 10) 
        //Explorer.editor.trigger('keyboard', 'type', {text: "test"});


}

Explorer.synchroRefresh = function ()
{
    // console.log(Explorer.vue.recordSession + ':' + Explorer.vue.recordSynchro);
    Explorer.ajaxLoad("json", { method: "replay", recordSession: Explorer.vue.recordSession });
};


// resize
Explorer.resize = function () {
    if (Explorer.editor) Explorer.editor.layout(); 
}
window.addEventListener('resize', Explorer.resize);

Vue.component('todo-item', {
  template: '\
    <li :class="isdir + \' l\' + level" :level="level" :filename="title">\
      <button v-on:click="$emit(\'remove\')">x</button>\
      <div class="dirname" v-on:click="toggleshow">{{ dirname }}</div>\
      <div class="basename" v-on:click="loadfile">{{ basename }}</div>\
      <div class="switch" v-if="isdir === \'D\'" v-on:click="toggleshow">*</div>\
    </li>\
  ',
  methods: {
    toggleshow: function () {
        curDir = event.target.parentNode;
        // console.log(curDir);
        curDir.classList.toggle('open');
        var mustShow = curDir.classList.contains('open');
        if(mustShow)
        {
            // show
            var curFilename = curDir.getAttribute('filename');
            if (Explorer.tabSearchDir.lastIndexOf(curFilename) < 0) {
                // new folder to explore
                Explorer.tabSearchDir.push(curFilename);
                
                Explorer.ajaxData.method = "listFile";
                Explorer.ajaxData.tabSearchDir = Explorer.tabSearchDir;
                Explorer.ajaxLoad("json", Explorer.ajaxData);
            }
        }
        curLevel = curDir.getAttribute('level');
        var next = curDir.nextSibling;
        while(next)
        {
            //console.log(next);
            nextLevel = next.getAttribute('level');
            nextF = next.classList.contains('F');
            if (nextLevel > curLevel)
            {
                if ((mustShow) && (nextLevel - curLevel  == 1))
                {
                    next.classList.remove('hide');
                }
                else
                {
                    // hide
                    next.classList.add('hide');
                    next.classList.remove('open');
                }
            }
            
            next = next.nextSibling;
            if (nextLevel <= curLevel)
            {
                next = null;
            }
        }
    },
    loadfile: function(event) {
        var line = event.target.parentNode;
        var filename = line.getAttribute('filename');
        var isDir = line.classList.contains('D');
        if (!isDir) {
            Explorer.ajaxLoad(filename);
            Explorer.updateOpen(filename);
        }
        else {
            this.toggleshow();
            Explorer.vue.currentDir = filename;
        }
    }
  },
  props: [ 'title', 'isdir', 'dirname', 'basename', 'level' ]
})

Explorer.lineNumber = 0;
Explorer.tabOpen = [];

Explorer.updateOpen = function (filename)
{
    var indexF = Explorer.tabOpen.indexOf(filename);
    if (indexF >= 0)
    {
        Explorer.tabOpen.splice(indexF, 1);
    }
    Explorer.tabOpen.unshift(filename);
    // keep in memory only last openMax
    Explorer.tabOpen.splice(Explorer.vue.openMax);
    // update local storage
    localStorage.setItem('tabOpen', JSON.stringify(Explorer.tabOpen));
}

Explorer.vue = new Vue({
  el: '.page',
  data: {
    ajaxMessage:    '',  
    currentFile:    '',  
    currentDir:     '',
    newTodoText:    '',
    recordSession:  'pizza-house-blue',
    recordSynchro:  "",
    recordReplayMs: 2000,
    todos:          <?php echo json_encode($explorer->tabData, JSON_PRETTY_PRINT); ?>,
    nextTodoId:     <?php echo 1+count($explorer->tabData) ?>,
    codeWindow:     'TEST WINDOW',
    tabWindow:      [],
    editorSelection:'',
    ajaxSession:    '',
    range1:         20,
    range2:         15,
    range3:         40,
    range4:         25,
    order1:         1,
    order2:         2,
    order3:         3,
    order4:         4,
    openMax:        20,
    lineCode:       '',
    helpUrl:        location.origin,
    saveRefresh:    false,
    myDebug:        ''
  },
  computed: {
        tabOpen: function () {
            if (Explorer.tabOpen.length == 0)
            {
                // load from local storage
                var lsTabOpen = localStorage.getItem('tabOpen');
                if (lsTabOpen) {
                    Explorer.tabOpen = JSON.parse(lsTabOpen);
                }    
            }
            return Explorer.tabOpen;    
      }
  },
  methods: {
    helpGoUrl: function (){
        //console.log(event);
        if (this.helpUrl != Explorer.helpFrame.src) 
            Explorer.helpFrame.src = this.helpUrl;
        else
            Explorer.helpFrame.contentWindow.location.reload(true);
    },
    updateHelpUrl: function (){
        //console.log(event);
        try {
            var nextUrl = event.target.contentWindow.location.href;
            if (this.helpUrl != nextUrl) this.helpUrl = nextUrl;
        }
        catch(error)
        {
            console.log(error);
        }
    },
    switchSynchro: function () {
        if (Explorer.synchroTimer)
        {
            clearInterval(Explorer.synchroTimer);
        }
        if (Explorer.vue.recordSynchro == "replay")
        {
            if (Explorer.vue.recordReplayMs < 400) Explorer.vue.recordReplayMs = 400;
            Explorer.synchroTimer = setInterval(Explorer.synchroRefresh, Explorer.vue.recordReplayMs);
        }
    },  
    enterList: function (line) {
        // console.log("enterList");
        // console.log(line);
        // add listeners
        line.addEventListener("mousemove", Explorer.actMouseMove, false);
        line.addEventListener("drop", Explorer.actDrop, false);
        line.addEventListener("dragenter", Explorer.actDragEnter, false);
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            line.addEventListener(eventName, Explorer.preventDefaults, false)
        });
    },  
    resizeEditor: Explorer.resize,  
    savecode: function () {
        event.preventDefault();
        alert('SAVE');
    },  
    reOpen: function () {
        event.preventDefault();

        // keep code in memory
        var curCode   = Explorer.editor.getValue();
        var itemKey0  = 'code@' + Explorer.vue.currentFile;
        var itemKeyLN  = 'ln@' + Explorer.vue.currentFile;
        var curLN = Explorer.editor.getPosition().lineNumber;
        localStorage.setItem(itemKey0, curCode);
        localStorage.setItem(itemKeyLN, curLN);
        // console.log('save LN' + curLN);

        // target
        var filename = event.target.innerHTML;
        var itemKey  = 'code@' + filename;
        // console.log(itemKey);
        var itemVal  = localStorage.getItem(itemKey);
        if (itemVal) {
            // UPDATE EDITOR CODE
            Explorer.vue.currentFile = filename;
	        Explorer.editorModel.setValue(itemVal);
            
            // line number
            var recordKeyLN  = 'ln@' + Explorer.vue.currentFile;
            var recordLine       = localStorage.getItem(recordKeyLN);
            if (recordLine) {
                recordLine = parseInt(recordLine);
                // console.log('load LN' + recordLine);
                Explorer.editor.revealLineInCenter(recordLine, 0);
                Explorer.editor.setPosition({column: 1, lineNumber: recordLine});
            }
            // code lang
            var codeLang    = localStorage.getItem('ext@' + Explorer.vue.currentFile);
            if (codeLang)
                monaco.editor.setModelLanguage(Explorer.editorModel, codeLang);

        }
        else {
            //alert('reOpen' + filename);
            Explorer.ajaxLoad(filename);
        }

    },  
    loadfile: function (event) {
        var filename=event.target.parentNode.getAttribute('filename');
        Explorer.ajaxLoad(filename);
        Explorer.updateOpen(filename);
    },  
    actLogin: function ()
    {
        //event.preventDefault();
        var email = Explorer.val("input[name=email]");
        var password = Explorer.val("input[name=password]");
        // alert(email + '/' + password);
        Explorer.ajaxLoad("json", { 
            method:     'login',
            loginE:      email,
            loginP:      password
        });
    },  
    actLogout: function ()
    {
        // event.preventDefault();
        Explorer.ajaxLoad("json", { 
            method:     'logout'
        });
    },  
    addNewTodo: function () {
        this.todos.push({
            id: this.nextTodoId++,
            title: this.newTodoText,
            isdir: "F",
            dirname: "/",
            basename: this.newTodoText,
            level:0
        })
      this.newTodoText = '';
    }
  }
})
                </script>
<script src="media/js/expand-full.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.13.1/min/vs/loader.js"></script>
<script>
// https://github.com/Microsoft/monaco-editor-samples/blob/master/sample-editor/index.html
require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.13.1/min/vs' }});
window.MonacoEnvironment = {
	getWorkerUrl: function(workerId, label) {
		return '?monaco-editor-worker-loader=proxy.js';
	}
};
require(['vs/editor/editor.main'], function() {
    Explorer.explorerZone = document.querySelector(".explorer-zone");
    Explorer.editorBox = document.querySelector(".editor-box");
    Explorer.editor = monaco.editor.create(Explorer.editorBox, {
    	value: "",
    	language: "php",
    	lineNumbers: "on",
    	roundedSelection: false,
    	scrollBeyondLastLine: false,
    	readOnly: false,
    	theme: "vs-dark",
    });    
    
	Explorer.editorModel = monaco.editor.createModel("", "php", "page.php");
    Explorer.editor.setModel(Explorer.editorModel);

    // https://microsoft.github.io/monaco-editor/api/interfaces/monaco.editor.icodeeditor.html#ondidchangecursorposition
    Explorer.editor.onDidChangeCursorPosition(Explorer.updatePositionEditor);
    Explorer.editor.onDidChangeCursorSelection(Explorer.updateSelectionEditor);
/*
<nav>
    <ul>
        <li><a href="test1"></a></li>
        <li><a href="test2"></a></li>
        <li><a href="test3"></a></li>
    </ul>
</nav>
*/    
    Explorer.escapeHtml = function (text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // autocomplete
    Explorer.configCompletion = {
        /*
        resolveCompletionItem: function(item) {
            console.log(item);
            return item;
        },
        */
        provideCompletionItems: function (model, position, token, context) {
            // is activated with Ctl-space...
            var lineRange = {startLineNumber: position.lineNumber, startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column};
            var lineCode = model.getValueInRange(lineRange);
            lineCode = lineCode.trim();
            // console.log(token);
            var tabCompletion = [];

            // example: not useful
            /*
            tabCompletion.push( {
                    label: lineCode + ' with ()',    // text for option
                    kind: monaco.languages.CompletionItemKind.Property,
                    documentation: '(' + lineCode + ')',
                    insertText: '(' + lineCode + ')'
                }); 
            */
            
            // emmet
            var emmetResult = '';
            try {
                emmetResult = emmet.expand(lineCode);
                tabCompletion.push( {
                        label: lineCode + ' (emmet code...)', // + Explorer.escapeHtml(lineCode),    // text for option
                        kind: monaco.languages.CompletionItemKind.Property,
                        documentation: '', // emmetResult, // Explorer.escapeHtml(emmetResult),
                        insertText: emmetResult,
                        range: lineRange    // expand edition range
                    });
                Explorer.vue.lineCode = emmetResult;
            }
            catch(error) {
                Explorer.vue.lineCode = '';
                console.log(error);    
            }
            // console.log(tabCompletion);
            return { isIncomplete: true, items: tabCompletion};
            // return tabCompletion;
        }
    };

    [ 'php', 'html', 'css', 'javascript', 'markdown' ]
        .forEach((lang) => monaco.languages.registerCompletionItemProvider(lang, Explorer.configCompletion));
    // dsjkssfdlskdfjs  sdfdsfj tab bhhj
    
    Explorer.saveCode = function(){
        // keep code in memory
        var curCode   = Explorer.editor.getValue();
        var itemKey0  = 'code@' + Explorer.vue.currentFile;
        var itemKeyLN  = 'ln@' + Explorer.vue.currentFile;
        var curLN = Explorer.editor.getPosition().lineNumber;

        localStorage.setItem(itemKey0, curCode);
        localStorage.setItem(itemKeyLN, curLN);

        Explorer.ajaxLoad("save", curCode);


    };

    // save button
    // Explorer.actSave = document.querySelector(".act-save");
    // Explorer.actSave.addEventListener("click", Explorer.saveCode);

    // keyboard shortcut
    Explorer.editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KEY_S, Explorer.saveCode);

    //Explorer.actRun = document.querySelector(".act-run");
    //Explorer.actRun.addEventListener("click", function(){
    //   var code = Explorer.editor.getValue();
    //   Explorer.ajaxLoad("run", code);
    //});

    Explorer.actRun2 = function ()
    {
        var target = this.getAttribute("data-filename");
        Explorer.ajaxLoad("run2", target);
    };

    Explorer.listRun2 = document.querySelectorAll(".act-run2");
    for(bouton of Explorer.listRun2)
    {
        bouton.addEventListener("click",  Explorer.actRun2);
    }
    
    Explorer.actReload = document.querySelector(".act-reload");
    Explorer.actReload.addEventListener("click", function(){
       location.reload();
    });
    
    Explorer.ajaxMessage = document.querySelector(".ajaxMessage");
    Explorer.ajaxMessage.addEventListener("click", function(){
       Explorer.vue.ajaxMessage = "";
    });

    Explorer.menuBox = document.querySelector(".menuBox");    
    // DRAG AND DROP
    Explorer.listLi = document.querySelectorAll(".todo-list li");
    for(line of Explorer.listLi)
    {
        line.addEventListener("mousemove", Explorer.actMouseMove, false);
        line.addEventListener("drop", Explorer.actDrop, false);
        line.addEventListener("dragenter", Explorer.actDragEnter, false);
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            line.addEventListener(eventName, Explorer.preventDefaults, false)
        });
    }

    Explorer.explorerZone.addEventListener("mouseout", function(){
        Explorer.menuBox.innerHTML = "";
    });
    

    Explorer.fileTarget = document.querySelector(".file-target");
    
    Explorer.tabListener = 
    [
        // lightbox
        {sel: '.lightbox', action: 'click', actionCB : function (event) {
            if (event.target == this) {
                Explorer.doCss({ sel: '.lightbox', remove: 'show'});
            }
        }},
        // menu
        {sel: '.act-crud-table', action: 'click', actionCB : function () {
            Explorer.doCss({ sel: '.lightbox', add: 'show'});
            Explorer.ajaxLoad("json", {method: 'buildScreen', screen: 'crudTable'});
        }},
        {sel: '.act-crud-line', action: 'click', actionCB : function () {
            Explorer.doCss({ sel: '.lightbox', add: 'show'});
            Explorer.ajaxLoad("json", {method: 'buildScreen', screen: 'crudLine'});
        }},
        {sel: '.lightbox form.ajax', action: 'submit', actionCB : function (event) {
            event.preventDefault();
            alert('HELLO...');
        }},
        {sel: '.act-run', action: 'submit', actionCB : function() {
            var code = Explorer.editor.getValue();
            Explorer.ajaxLoad("run", code);
        }},
        {sel: '.act-save', action: 'click', actionCB : Explorer.actSave},
        {sel: '.actResetOpen', action: 'click', actionCB : function (event) {
            event.preventDefault();
            Explorer.tabOpen.splice(0);
            if (Explorer.vue.currentFile != '')
                Explorer.tabOpen.push(Explorer.vue.currentFile);
            Explorer.vue.ajaxMessage = '...reset recent files...';
            // update local storage
            localStorage.clear(); // FIXME: maybe too violent ?
            localStorage.setItem('tabOpen', JSON.stringify(Explorer.tabOpen));
        }},
        {sel: '.actFile', action: 'click', actionCB : function(){
            var ft = Explorer.fileTarget.textContent;
            Explorer.ajaxLoad(this.value, ft, Explorer.vue.currentFile);
        }}
    ];
    for(var li of Explorer.tabListener)
    {
        Explorer.on(li.sel, li.action, li.actionCB);
    }

    // load the page in iframe
    Explorer.helpFrame = document.querySelector('.helpFrame');
    if (Explorer.helpFrame)
        Explorer.helpFrame.src = location.origin;    
});

// tooltip
Explorer.actMouseMove = function (event)
{
    Explorer.menuBox.style.position = 'fixed';
    Explorer.menuBox.style.top = event.pageY + "px";
    Explorer.menuBox.style.left = (event.pageX + 10) + "px";
    Explorer.menuBox.innerHTML = this.getAttribute("filename"); // + ' (' +event.pageX+ ',' +event.pageY+ ')';
    
}
</script>

    </body>
</html>