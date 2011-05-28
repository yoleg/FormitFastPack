
<!-- text -->
  <input type="[[+type]]" name="[[+name]]" id="[[+name]]" value="[[+current_value]]" class="[[+class]][[+error_class]]" size="40">
<!-- text -->

<!-- textarea -->
  <textarea id="[[+name]]" class="[[+type]] [[+class]][[+error_class]]" name="[[+name]]">[[+current_value]]</textarea>
<!-- textarea -->

<!-- checkbox -->
<input name="[[+name]]" type="hidden" value="[[+default]]" />
[[!field? &type=`bool` &subtype=`checkbox` &name=`[[+name]]` &label=`[[+value]]` &message=`[[+label]]` &tpl=`0`]]
<!-- checkbox -->

<!-- radio -->
<span class="boolWrap">
<input type="hidden" name="[[+name]]" value="[[+default]]" />
[[!Loop? &tpl=`fieldBool` &static=`name==[[+name]]||subtype==radio` &ph=`label,message` &loop=`[[+options]]` ]]
</span>
<!-- radio -->

<!-- submit -->
<input id="[[+name]]" class="button" name="[[+name]]" type="[[+type]]" value="[[+message:default=`Submit`]]" />
<a href="[[~[[*id]]]]" class="button">Cancel</a>
<!-- submit -->

<!-- select -->
<select name="[[+name]]" id="[[+name]]" class="[[+class]]">
  <option name="[[+name]]" value="[[+default]]">[[+label]]</option>
[[!Loop? &tpl=`fieldOption` &static=`name==[[+name]]` &ph=`label` &loop=`[[+options]]` ]]
</select>
<!-- select -->

<!-- static -->
<span class="static_field">[[!+[[+name]]]]</span>
<!-- static -->

<!-- bool -->
<span class="boolDiv [[+class]]">
  <input type="[[+subtype]]" class="[[+subtype]]" value="[[+label]]" name="[[+name]][[+array:notempty=`[]`]]" id="[[+name]][[+label:strip:stripString=` `:stripString=`/`]]" [[!+[[+prefix]][[+name]]:FormitIsChecked=`[[+label]]`]] /> 
<label for="[[+name]][[+label:strip:stripString=` `:stripString=`/`]]" class="[[+subtype]]" id="label[[+name]][[+idx]]">[[+message:default=`[[+label]]`]]</label></span>
<!-- bool -->
