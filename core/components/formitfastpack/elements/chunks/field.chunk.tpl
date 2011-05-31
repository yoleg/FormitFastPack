<div class="[[+outer_class]]" id="[[+name]]_wrap"> 
<label for="[[+name]]" title="[[+name:replace=`_== `:ucwords]]">[[+label:default=`[[+name:replace=`_== `:ucwords]]`]] [[+req:notempty=` *`]]</label>
[[+inner_html]]
[[!+[[+remote_prefix]][[+remote_field]]:notempty=`<span class="info"><em>From [[!+[[+remote_prefix]]providerName]]:</em> [[!+[[+remote_prefix]][[+remote_field]]]]</span>`]]
[[+note:notempty=`<span class="info"><em>[[+note]]</em></span>`]]
[[!+error:notempty=`<span class="error">[[!+error]]</span>`]] 
</div>