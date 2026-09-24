(function(){
'use strict';
const native=window.localStorage, API='api/store.php';
let serverReady=false;
try{
 const x=new XMLHttpRequest(); x.open('GET',API,false); x.send();
 if(x.status===200){const r=JSON.parse(x.responseText);if(r.success&&r.data){Object.keys(r.data).forEach(k=>native.setItem(k,r.data[k]));serverReady=true;}}
}catch(_){}
const bridge={
 get length(){return native.length;}, key(i){return native.key(i);}, getItem(k){return native.getItem(k);},
 setItem(k,v){native.setItem(k,String(v));if(serverReady&&/^jkt_/i.test(k)){let value;try{value=JSON.parse(String(v));}catch(_){value=String(v);}fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({key:k,value})}).catch(()=>{});}},
 removeItem(k){native.removeItem(k);if(serverReady&&/^jkt_/i.test(k))fetch(API,{method:'DELETE',headers:{'Content-Type':'application/json'},body:JSON.stringify({key:k})}).catch(()=>{});},
 clear(){native.clear();if(serverReady)fetch(API,{method:'DELETE',headers:{'Content-Type':'application/json'},body:'{}'}).catch(()=>{});}
};
try{Object.defineProperty(window,'localStorage',{configurable:true,value:bridge});}catch(_){}
})();