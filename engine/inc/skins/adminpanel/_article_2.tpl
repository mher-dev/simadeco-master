<div class="module">
    <div class="module-head">
        <h3>{article-title}</h3>
    </div>
    <div class="module-body">

        <form class="form-horizontal row-fluid">
            [article-name]
            <div class="control-group">
                <label class="control-label" for="article-name">URL del artículo</label>
                <div class="controls" title="Solo se aceptan simbolos alfanuméricos más {-_.,}">
                    <div class="input-prepend span8">
                        <div class="double-input">
                            <span class="add-on">#{article-id}</span>
                            <input id="article-name" class="span8" type="text" placeholder="prepend" value="{article-name}"  />
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
                    <input type="text" id="basicinput" placeholder="Título del artículo" value="{article-title}" class="span8">
                </div>
            </div>
            [/article-title]

            [short-story]
            <div class="control-group">
                <label class="control-label" for="short-story">Artículo resumido<br />(Opcional)</label>
                <div class="controls">
                    <textarea class="span8 ckeditor" id="short-story" rows="5">{short-story}</textarea>
                </div>
            </div>
            [/short-story]

            [full-story]
            <div class="control-group">
                <label class="control-label" for="full-story">Artículo completo</label>
                <div class="controls">
                    <textarea class="span8 ckeditor" id="full-story" rows="5">{full-story}</textarea>
                </div>
            </div>
            [/full-story]
            
            [creation-date]
            <div class="control-group">
                <label class="control-label" for="basicinput">Fecha de creación</label>
                <div class="controls">
                    <input type="date" id="basicinput" placeholder="Título del artículo" value="{date}" class="span4">
                </div>
            </div>
            [/creation-date]
            
            [author]
            <div class="control-group">
                <label class="control-label" for="basicinput">Autor</label>
                <div class="controls">
                    <div class="btn-group">
                        <a class="btn btn-primary" href="#"><i class="icon-user icon-white"></i> {author}</a>
                        <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="icon-pencil"></i> Cambiar</a></li>
                            <li class="divider"></li>
                            <li><a href="#"><i class="icon-ban-circle"></i> Banear</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            [/author]
            
            <div class="control-group">
                <label class="control-label" for="basicinput">Disabled Input</label>
                <div class="controls">
                    <input type="text" id="basicinput" placeholder="You can't type something here..." class="span8" disabled="">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="basicinput">Tooltip Input</label>
                <div class="controls">
                    <input data-title="A tooltip for the input" type="text" placeholder="Hover to view the tooltip…" data-original-title="" class="span8 tip">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="basicinput">Prepended Input</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on">#</span><input class="span8" type="text" placeholder="prepend">       
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="basicinput">Appended Input</label>
                <div class="controls">
                    <div class="input-append">
                        <input type="text" placeholder="5.000" class="span8"><span class="add-on">$</span>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="basicinput">Dropdown Button</label>
                <div class="controls">
                    <div class="dropdown">
                        <a class="dropdown-toggle btn" data-toggle="dropdown" href="#">Dropdown Button <i class="icon-caret-down"></i></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <li><a href="#">First Row</a></li>
                            <li><a href="#">Second Row</a></li>
                            <li><a href="#">Third Row</a></li>
                            <li><a href="#">Fourth Row</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="basicinput">Dropdown</label>
                <div class="controls">
                    <select tabindex="1" data-placeholder="Select here.." class="span8">
                        <option value="">Select here..</option>
                        <option value="Category 1">First Row</option>
                        <option value="Category 2">Second Row</option>
                        <option value="Category 3">Third Row</option>
                        <option value="Category 4">Fourth Row</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Radiobuttons</label>
                <div class="controls">
                    <label class="radio">
                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked="">
                        Option one
                    </label> 
                    <label class="radio">
                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
                        Option two
                    </label> 
                    <label class="radio">
                        <input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
                        Option three
                    </label>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Inline Radiobuttons</label>
                <div class="controls">
                    <label class="radio inline">
                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked="">
                        Option one
                    </label> 
                    <label class="radio inline">
                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
                        Option two
                    </label> 
                    <label class="radio inline">
                        <input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
                        Option three
                    </label>
                </div>
            </div>


            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">Submit Form</button>
                </div>
            </div>
        </form>
    </div>
</div>