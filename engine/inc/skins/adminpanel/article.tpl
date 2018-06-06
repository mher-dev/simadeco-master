
<div class="module">
    <div class="module-head">
        <h3>{article-display-title}</h3>
    </div>
    <div class="module-body">
        <form class="form-horizontal row-fluid" action="" method="post">
            [article-name]
            <div class="control-group">
                <label class="control-label" for="article-name">URL del artículo</label>
                <div class="controls" title="Solo se aceptan simbolos alfanuméricos más (_)">
                    <div class="input-prepend span8">
                        <div class="double-input">
                            <span class="add-on">#{article-id}</span>
                            <input id="a_name" readonly="readonly" name="a_name" onclick="change_state(this, true, '_auto_name')" onkeyup="trim_special_chars(this);" onchange="trim_special_chars(this);" class="span8" type="text" maxlength="255" placeholder="Nombre SEO" value="{article-name}" {editable} />
                        </div>
                        <div>
                            [auto-name]
                            <label class="checkbox">
                                <input id="_auto_name" type="checkbox" {auto-name-checked} onclick="javascript:(document.getElementById('a_name').readOnly = this.checked); duplicate_trimmed('a_title', 'a_name', '_auto_name');">
                                Asignar automaticamente
                            </label>
                            [/auto-name]
                        </div>
                    </div>
                    <span class="help-inline">Máximo de 255 símbolos</span>        
                </div>
            </div>
            [/article-name]
            [article-title]
            <div class="control-group">
                <label class="control-label" for="basicinput">Título</label>
                <div class="controls">
                    <input type="text" id="a_title" onkeyup="duplicate_trimmed(this, 'a_name', '_auto_name');" onchange="duplicate_trimmed(this, 'a_name', '_auto_name')" name="a_title" placeholder="Título del artículo" value="{article-title}" class="span8" {editable} />
                </div>
            </div>
            [/article-title]

            [short-story]
            <div class="control-group">
                <label class="control-label" for="a_short_content">Artículo resumido<br />(Opcional)</label>
                <div class="controls">
                    <textarea class="span8 ckeditor" id="a_short_content" name="a_short_content" rows="5" {editable}>{short-story}</textarea>
                </div>
            </div>
            [/short-story]

            [full-story]
            <div class="control-group">
                <label class="control-label" for="a_full_content">Artículo completo</label>
                <div class="controls">
                    <textarea class="span8 ckeditor" id="a_full_content" name="a_full_content"rows="5" {editable}>{full-story}</textarea>
                </div>
            </div>
            [/full-story]

            [creation-date]
            <div class="control-group">
                <label class="control-label" for="a_create_date">Fecha de creación</label>
                <div class="controls">
                    <input type="text" id="a_create_date" name="a_create_date" placeholder="Título del artículo" value="{date}" class="span4" {editable}>
                </div>
            </div>
            [/creation-date]

            [author]
            <div class="control-group">
                <label class="control-label" for="basicinput">Autor</label>
                <div class="controls">
                    <div class="btn-group">
                        <a class="btn" href="#"><i class="icon-user icon-white"></i>{author}</a>
                        [editable=author]
                        <input type="hidden" value="{author-id}" name="u_id" />
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="icon-pencil"></i> Cambiar</a></li>
                            <li class="divider"></li>
                            <li><a href="#"><i class="icon-ban-circle"></i> Banear</a></li>
                        </ul>
                        [/editable]
                    </div>
                </div>
            </div>
            [/author]

            <!-- SOTANO: Controles de Guardar/Cancerlar.. -->
            <div class="control-group">
                <div class="controls">

                    [editable=cancel-button]
                    <!-- Button to trigger modal -->
                    <a href="#alertaCancelar" role="button" class="btn" data-toggle="modal"><i class="icon-remove"></i> Cancelar</a>
                    <!-- Modal -->
                    <div id="alertaCancelar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="alertaCancelarLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#215;</button>
                            <h3 id="alertaCancelarLabel">Esta saliendo sin guardar</h3>
                        </div>
                        <div class="modal-body">
                            <p>
                            <ul>
                                <li>Esta saliendo sin salvar los cambias realizados.</li>
                                <li>¿Está seguro de que es eso lo que quiere hacer?</li>
                            </ul></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success" data-dismiss="modal" aria-hidden="true"><i class="icon-chevron-left"></i> Volver</button>
                            <a class="btn btn-primary" href="{HOME_DIR}/{ADMIN_FILE}?do=listview&action=articles"><i class="icon-remove"></i> Salir sin guardar</a>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="icon-save"></i> Guardar</button> 
                    [/editable]
                    [not-editable]
                    <a class="btn btn-primary" href="{HOME_DIR}/{ADMIN_FILE}?do=listview&action=articles">Aceptar</a>
                    [/not-editable]

                    [delete]
                    <!-- Button to trigger modal -->
                    <a class="btn btn-primary" href="{HOME_DIR}/{ADMIN_FILE}?do=listview&action=articles"><i class="icon-chevron-left"></i> Cancelar</a>
                    <a href="#alertaAceptar" role="button" class="btn btn-warning" data-toggle="modal"><i class="icon-remove"></i> Eliminar</a>
                    <!-- Modal -->
                    <div id="alertaAceptar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="alertaAceptarLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#215;</button>
                            <h3 id="alertaAceptarLabel">Esta eliminando un artículo</h3>
                        </div>
                        <div class="modal-body">
                            <p>
                            <ul>
                                <li>Esta intentando eliminar de forma definitiva un artículo.</li>
                                <li>¿Está seguro de que es eso lo que quiere hacer?</li>
                            </ul></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><i class="icon-chevron-left"></i> No, Volver</button>
                            <button class="btn btn-warning" type="submit"><i class="icon-remove"></i> Sí, Eliminar</button>
                        </div>
                    </div>
                    [/delete]
                    <input type="hidden" value="{input-action}" name="input-action"/>

                </div>
            </div>
        </form>
        <script type="text/javascript">
            document.getElementById('a_name').readOnly = document.getElementById('_auto_name').checked;
        </script>            
    </div>
</div>