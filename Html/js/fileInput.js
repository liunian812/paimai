/*!
 * M.YS.COM v0.0.0
 * Copyright 2015
 * Author burning <iburning@live.cn>
 */
var FileInput=function(a){var b=this;b.id="",b.filePathInputName="file",b.el=null,b.tpl='<div class="file" id="file{{id}}">    <div class="preview"></div>    <div class="progress"></div>    <a class="remove" href="javascript:;" style="display:none">X</a>    <a class="rotate" href="javascript:;" style="display:none">R</a>    <input type="hidden" name="{{filePath}}" value="" />    <input type="hidden" name="rotate" value="0" />  </div>',b.init(a)};FileInput.prototype.init=function(a){var b=this;a=a||{},b.id=a.id||b.id,b.filePathInputName=a.filePathInputName||b.filePathInputName;var c=a.tpl||b.tpl;c=c.replace("{{id}}","-"+b.id),c=c.replace("{{filePath}}",b.filePathInputName);var d=b.el=$(c);b.preview=d.find(".preview"),b.progress=d.find(".progress"),b.btnRemove=d.find(".remove"),b.btnRotate=d.find(".rotate"),b.filePath=d.find('input[name="'+b.filePathInputName+'"]'),b.rotate=d.find('input[name="rotate"]'),b._bindEvent()},FileInput.prototype._bindEvent=function(){var a=this;a.btnRemove.on("click",function(b){a.remove()}),a.btnRotate.on("click",function(b){a.rotateImage()})},FileInput.prototype.uploadSuccess=function(a){var b=this;b.progress.html("上传成功"),a&&(b.setFileValue(a),b.setImageSrc(a)),b.showOptions()},FileInput.prototype.setFileValue=function(a){var b=this;b.filePath.val(a)},FileInput.prototype.setImageSrc=function(a){var b=this,c=new Image;c.src=a,c.onload=function(){b.preview.html("");var a=$(c);a.addClass("img-preview"),b.preview.append(a)}},FileInput.prototype.showOptions=function(){var a=this;a.btnRemove.css("display",""),a.btnRotate.css("display","")},FileInput.prototype.remove=function(){var a=this;a.el.remove(),"function"==typeof a.onRemove&&a.onRemove()},FileInput.prototype.rotateImage=function(){var a=this;console.log(a.rotate.val());var b=parseInt(a.rotate.val())+90;b>=360&&(b=0),console.log(b),a.rotate.val(b);var c="rotate("+b+"deg)";a.preview.css({"-webkit-transform":c,"-moz-transform":c,"-ms-transform":c,"-o-transform":c,transform:c})};