License: GPL v.2 or later
Contact: Oleg Pryadko <oleg@websitezen.com>

Snippets:
* field - Generates a form field (and optionally sub-fields, like a set of radio buttons). Features smart caching, inserting the current FormIt values, only two templates for all form fields, highly flexible customization, and more. See the example chunks in the chunks directory for usage examples.
* fiGenerateReport and fiProcessArrays (hooks) - can be used to generate a report dynamically to use with all of your FormIt forms. See http://modxcms.com/forums/index.php?topic=64656.0

Chunks:
* exampleFormItForm.chunk.tpl - an example FormIt form to get you started. Some of these fields are not yet templated!
* exampleFormItEmailReport.chunk.tpl - An example FormIt email report template. You can use a single email report template for every single form on your site.
* fieldTypes.chunk.tpl - the default &tpl chunk. Copy and paste its contents into a chunk called "fieldTypes" and edit to suit your needs.
* field.chunk.tpl - the default &outer_tpl chunk. Copy and paste its contents into a chunk called "field" and edit to suit your needs.
* formReportRow.chunk.tpl - the default fiGenerateReport row template. Note how output filters are used in combination with a naming convention to generate proper labels from field names.

Notes:
* Adjusting the prefix will theoretically make the field snippet work with the EditProfile and Register snippets as well.
