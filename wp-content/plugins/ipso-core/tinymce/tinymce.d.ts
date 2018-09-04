declare module tinymce{
    class Editor{
        controlManager:ControlManager;
        id:string;
        settings:any;
        isNotDirty:boolean;
        plugins:any;
        formatter:Formatter;
        windowManager:WindowManager;
        selection:dom.Selection;
        addCommand(name: string, callback: (ui:boolean, value:any)=>any, scope?:any);
        addButton(name: string, settings: ButtonSettings);
        onNodeChange:EventCollection;
        dom:dom.DOMUtils;
        execCommand(cmd:string, ui:boolean, value?:any, args?:any);
    }

    var PluginManager:PluginManagerClass;

    class PluginManagerClass {
        add(name:string, plugin:Plugin);
    }

    function create(fqName:string, implementation:any);

    class ControlManager{
        get(id: string):ui.Control;
        setActive(id:string, s:boolean):tinymce.ui.Control;
        setDisabled(id:string, state:boolean):tinymce.ui.Control;
        add(control:ui.Control):ui.Control;
        createControl(name:string):ui.Control;
        createDropMenu(id:string, settings?:any, controlClass?:any):ui.Control;
        createListBox(id:string, settings?:any, controlClass?:any):ui.Control;
        createButton(id:string, settings?:any, controlClass?:any):ui.Control;
        createMenuButton(id:string, settings?:any, controlClass?:any):ui.Control;
        createSplitButton(id:string, settings?:any, controlClass?:any):ui.Control;
        createColorSplitButton(id:string, settings?:any, controlClass?:any):ui.Control;
        createToolbar(id:string, settings?:any, controlClass?:any):ui.Control;
        createSeparator( controlClass?:any):ui.Control;
        setControlType(name:string, classReference:Function);
        destroy():void;
    }

    class Formatter{
        get(name:string):any;
        register(name:any, format:any);
        apply(name:string, vars:any, node:Node);
        remove(name:string, vars:any, node:Node);
        remove(name:string, vars:any, node:Range);
        toggle(name:string, vars:any, node:Node);
        matchNode(node:Node, name:string, vars:any, similar:boolean):any;
        match(name:string, vars:any, node:Node):boolean;
        matchAll(names:string[], vars:any[]):any[];
        canApply(name:string):boolean;
        formatChanged(formats:string, callback:(state:any, args:any[])=>void);
    }

    class WindowManager{
        open(options?: WindowManagerOptions, params?:WindowManagerParameters );
        close(window:Window);
        createInstance(className:string):any;
        confirm(title:string, callback:(result:string)=>void, scope?:any);
        alert(title:string, callback:(result: string)=>void, scope?:any);
    }


    class Plugin{
        init(editor: tinymce.Editor, url:string);
        createControl(name:string, controlManager: tinymce.ControlManager): tinymce.ui.Control;
        getInfo():TinyMCEPluginInfo;
    }
    interface TinyMCEPluginInfo{
        longname: string;
        author:string;
        authorurl: string;
        infourl: string;
        version: string;
    }

    module ui{
        class Control{
            setDisabled(state:boolean);
            isDisabled():boolean;
            setActive(state:boolean);
            isActive():boolean;
            setState(className:string, state:boolean);
            isRendered():boolean;
            renderHTML():string;
            renderTo(element:Element);
            postRender();
            remove();
            destroy();
        }

        class Container extends Control{
            add(control:Control):Control;
            get(name:string):Control;

        }
    }
    module dom{
        //http://www.tinymce.com/wiki.php/API3:class.tinymce.dom.DOMUtils
        class DOMUtils{
            isBlock(element:Element):boolean;
            isBlock(element:string):boolean;
            getRoot():Element;
            getViewPort(w:Window):any;
            getRect(element:Element):Rectangle;
            getRect(element:string):Rectangle;
            getDimension(element:Element):Dimension;
            getDimension(element:string):Dimension;
            getParent(node:Node, filter: Function, root?:Node):Node;
            getParent(node:string, filter: Function, root?:Node):Node;
            getParents(node:Node, filter: Function, root?:Node):Node[];
            getParents(node:string, filter: Function, root?:Node):Node[];
            get(id:string):Element;
            get(element:Element):Element;
            getNext(node:Node, selector:string):Node;
            getNext(node:Node, selector:Function):Node;
            getPrev(node:Node, selector:string):Node;
            getPrev(node:Node, selector:Function):Node;
            select(cssPattern:string, root?:any):Node[];
            is(node:Node, selector: string):boolean;
            is(nodes:NodeList, selector:string):boolean;
            add(id:string, name:string, args?:any, html?:string, createOrAdd?:boolean):Element;
            add(id:string[], name:string, args?:any, html?:string, createOrAdd?:boolean):Element[];
            add(element:Element, name:string, args?:any, html?:string, createOrAdd?:boolean):Element;
            add(element:Element[], name:string, args?:any, html?:string, createOrAdd?:boolean):Element[];
            add(id:string, existing: Element, args?:any, html?:string, createOrAdd?:boolean):Element;
            add(id:string[], existing: Element, args?:any, html?:string, createOrAdd?:boolean):Element[];
            add(element:Element, existing: Element, args?:any, html?:string, createOrAdd?:boolean):Element;
            add(element:Element[], existing: Element, args?:any, html?:string, createOrAdd?:boolean):Element[];
            create(name:string, attributes?:any, html?:string):Element;
            createHTML(name:string, attributes?:any, html?:string):string;
            remove(id:string, keepChildren?:boolean):Element;
            remove(element:Element, keepChildren?:boolean):Element;
            remove(ids:string[], keepChildren?:boolean):Element[];
            remove(elements:Element[], keepChildren?:boolean):Element[];
        }

        class EventUtils{
            add(element:any, eventName:string, callback:(evt:any)=>void, scope?:string):Function;
            remove(element:any, eventName:string, callback:(evt:any)=>void):boolean;
            clear(obj:any);
            cancel(evt:any):boolean;
            stop(evt:any):boolean;
            prevent(evt:any):boolean;
            destroy();
        }

        class Selection{
            destroy():void;
            getContent(settings?:any):string;
            setContent(content:string, args?:any):void;
            getStart():Element;
            getEnd():Element;
            getBookmark(type?:number, normalized?:boolean):any;
            moveToBookmark(bookmark:any):boolean;
            select(node:Element, content?:boolean) : Element;
            isCollapsed():boolean;
            isForward():boolean;
            collapse(to_start?:boolean);
            getSelectedBlocks():Element[];
            getSel():Selection;
            setCursorLocation(i:any, j:any);
            scrollIntoView(flag?:boolean);
            getRng(w3c:boolean):Range;
            setRng(range: Range);
            setNode(node:Element):Element;
            getNode():Element;
            selectorChanged(selector:string, callback:Function);
            onBeforeSetContent:EventCollection;
            onBeforeGetContent:EventCollection;
            onSetContent:EventCollection;
            onGetContent:EventCollection;
        }
    }
}

declare class TinyMCEPopup{
    alert(title: string, callback:(result:boolean)=>void, scope?:any):void;
    close();
    confirm(title: string, callback:(result:boolean)=>void, scope?:any):void;
    execCommand(command:string, ui?:boolean, values?:string, args?:any);
    executeOnLoad(toExec:string);
    getLang(name:string, defaultValue?:string):string;
    getParam(name:string, defaultValue?:string):string;
    getWin():Window;
    getWindowArg(name:string, defaultValue?:string):string;
    init();
    openBrowser(elementId:string, type:BrowserType, optionName:string);
    pickColor(evt:Event, elementId:string);
    requireLangPack();
    resizeToInnerSize();
    restoreSelection();
    storeSelection();
    editor:tinymce.Editor;
    onInit:EventCollection;
}
declare enum BrowserType{
    image,
    file,
    flash
}
declare var tinyMCEPopup:TinyMCEPopup;

interface EventCollection{
    add(handler:(editor:tinymce.Editor, controlManager: tinymce.ControlManager, evt:any)=>void);
}

interface WindowManagerOptions{
    title?:string;
    file?:string;
    width?:number;
    height?:number;
    resizable?:boolean;
    maximizable?:boolean;
    inline?:boolean;
    popup_css?:any;//string/boolean
    translate_i18n?:boolean;
    close_previous?: any;//string/boolean
    scrollbars?: any;//string/boolean
}
interface WindowManagerParameters{
    plugin_url?: string;
}
interface Dimension{
    w:number;
    h:number;
}
interface Rectangle extends Dimension{
    x:number;
    y:number;
}

interface ButtonSettings {
    title?:string;
    image?:string;
    onclick?: ()=>void
}

declare class TinyMCEStatic{
    PluginManager: TinyMCEPluginManager;
    activeEditor:tinymce.Editor;
}

declare var tinyMCE:TinyMCEStatic;

declare class TinyMCEPluginManager{
    add(pluginName:string, callback: (editor: tinymce.Editor, url:string)=>void);
}
