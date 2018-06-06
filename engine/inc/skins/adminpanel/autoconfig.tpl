<div class="module">
    <form method="post">
    <div class="module-body">
        <div class="profile-head media">
            <a href="#" class="media-avatar pull-left">
                <img src="{module-icon}" alt="{module-title}">
            </a>
            <div class="media-body">
                <h4>
                    {module-title}
                </h4>
                <p class="profile-brief">
                    {module-description}
                </p>
                [shortcuts]
                <div class="profile-details muted">
                    [shortcut]
                    <a href="{shortcut-url}" class="btn"><i class="icon-{shortcut-icon} shaded"></i>{shortcut-content}</a>
                    [/shortcut]
                </div>
                [/shortcuts]


            </div>
        </div>

        [tabs-headers]
        <ul class="profile-tab nav nav-tabs">
            {tab-header}
            [tab-header]
            <li class="{tab-active}"><a href="#{tab-name}" data-toggle="tab">{tab-title}</a></li>
            [/tab-header]
        </ul>
        [/tabs-headers]

        [tabs-contents]
        <div class="profile-tab-content tab-content">
            {tab-content}
            [tab-content]
            <div class="tab-pane fade {tab-active}" id="{tab-name}">
                <div class="stream-list">
                    <div class="controls">
                        {config-row}
                    </div>    
                        [config-row]
                        <div class="media stream">
                        <div class="col-lg-7"><b>{config-title}</b><br />{config-description}</div>
                        <div class="col-lg-4">{config-control}</div>
                        </div>
                        [/config-row]
                </div>
            </div>
            [/tab-content]
        </div>
        [/tabs-contents]
    </div>
    <!--/.module-body-->
    <div class="modal-footer">
        <div class="controls" style="text-align: center;">


            <!-- Button to trigger modal -->
            <a href="#alertaCancelar" role="button" class="btn btn-warning" data-toggle="modal"><i class="icon-remove"></i> Cancelar</a>
            <!-- Modal -->
            <button formnovalidate="formnovalidate" type="submit" class="btn btn-primary"><i class="icon-save"></i> Guardar</button><div id="alertaCancelar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="alertaCancelarLabel" aria-hidden="true" style="display: none; text-align: left;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="alertaCancelarLabel">Esta saliendo sin guardar</h3>
                </div>
                <div class="modal-body">
                    <p>
                    </p><ul>
                        <li>Esta saliendo sin salvar los cambias realizados.</li>
                        <li>¿Está seguro de que es eso lo que quiere hacer?</li>
                    </ul><p></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" data-dismiss="modal" aria-hidden="true"><i class="icon-chevron-left"></i> Volver</button>
                    <a class="btn btn-primary" href="{HOME_DIR}/{ADMIN_FILE}"><i class="icon-remove"></i> Salir sin guardar</a>
                </div>
            </div>
            <input type="hidden" value="save" name="input-action">

        </div>
    </div>
    </form>            
</div>
[controls=dropdown]
<select name="{name}" tabindex="{tabindex}" class="col-xs-12" required="required" {enabled}>
        {options}
        [option]
        <option value="{option-value}" {option-selected}>{option-description}</option>
        [/option]
</select>
[/controls]
[controls=textbox]
<div class="autoconfig textbox">
<input type="text" value="{value}" name="{name}" tabindex="{tabindex}" class="col-xs-12" required="required" {enabled}/></div>
[/controls]
<!--            [tab-content]
            <div class="tab-pane fade {tab-active}" id="{tab-name}">
                <table class="table table-striped table-bordered table-condensed">
                    <tbody>
                        {config-row}
                        [config-row]
                        <tr>
                            <td><b>{config-title}</b><br />{config-description}</td>
                            <td>{config-control}</td>
                        </tr>
                        [/config-row]
                    </tbody>
                </table>            
            </div>
            [/tab-content]!-->