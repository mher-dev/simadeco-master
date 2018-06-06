[shortcuts]
<div class="btn-controls">
    <div class="btn-box-row row-fluid">
        <a href="{HOME_DIR}/{ADMIN_FILE}?do=articles&action=create" class="btn-box small span2">
            <i class="icon-plus"></i>
            <b>Nuevo artículo</b>
        </a>
    </div>
</div>
[/shortcuts]
[listview]
<div class="module">
    <div class="module-head">
        <h3>{title}</h3>
    </div>
    <div class="module-body table">
        <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
            <thead>
                <tr>
                    {header}
                </tr>
            </thead>
            <tbody>
                [row=odd]
                <tr class="odd gradeX {selected}">
                    {rows}
                </tr>
                [/row]

                [row=even]
                <tr class="even gradeC {selected}">
                    {rows}
                </tr>
                [/row]
                {content}
            </tbody>
            [footer]
            <tfoot>
                <tr>
                    {footer}
                </tr>
            </tfoot>
            [/footer]
        </table>
    </div>
</div><!--/.module-->
[/listview]