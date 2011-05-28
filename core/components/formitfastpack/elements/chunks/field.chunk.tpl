<div>  
<p> <label for="[[+name]]" title="[[+name:replace=`_== `:ucwords]]">[[+label:default=`[[+name:replace=`_== `:ucwords]]`]] [[+req:notempty=` *`]]</label>

[[Case? &subject=`[[+type]]`
  
&case1=`text` &then1=`
  <input type="[[+type]]" name="[[+name]]" id="[[+name]]" value="[[+current_value]]" class="[[+class]][[+error_class]]" size="40">
`
&case2=`textarea` &then2=`
  <textarea id="[[+name]]" class="[[+type]] [[+class]][[+error_class]]" name="[[+name]]">[[+current_value]]</textarea>
`
&case3=`checkbox` &then3=`
<input name="[[+name]]" type="hidden" value="[[+default]]" />
[[!$fieldBool? &type=`checkbox` &name=`[[+name]]` &label=`[[+value]]` &message=`[[+label]]`]]
`
&case4=`radio` &then4=`
<span class="boolWrap">
<input type="hidden" name="[[+name]]" value="[[+default]]" />
[[!Loop? &tpl=`fieldBool` &static=`name==[[+name]]` &ph=`label,message` &loop=`[[+options]]` ]]
</span>
`
&case5=`submit` &then5=`
<input id="[[+name]]" class="button" name="[[+name]]" type="[[+type]]" value="[[+message:default=`Submit`]]" />
<a href="[[~[[*id]]]]" class="button">Cancel</a>
`
&case6=`select` &then6=`
<select name="[[+name]]" id="[[+name]]" class="[[+class]]">
  <option name="[[+name]]" value="[[+default]]">[[+label]]</option>
[[!Loop? &tpl=`fieldOption` &static=`name==[[+name]]` &ph=`label` &loop=`[[+options]]` ]]
</select>
`
&case7=`static` &then7=`
<span class="static_field">[[!+[[+name]]]]</span>
`
]]
  [[!+[[+remote_prefix]][[+remote_field]]:notempty=`<span class="info"><em>From [[!+[[+remote_prefix]]providerName]]:</em> [[!+[[+remote_prefix]][[+remote_field]]]]</span>`]]
[[+note:notempty=`<span class="info"><em>[[+note]]</em></span>`]]
[[+error:notempty=`<span class="error">[[!+error]]</span>`]]
</p>
</div>