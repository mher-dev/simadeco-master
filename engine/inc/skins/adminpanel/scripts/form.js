function duplicate_trimmed(inputField, outputField, controlCheckBox/*OPCIONAL*/)
{
    //Asignación de valores de por defecto
    controlCheckBox = typeof controlCheckBox !== 'undefined' ? document.getElementById(controlCheckBox).checked : false;
    inputField = typeof inputField !== 'string' ? inputField : document.getElementById(inputField);

    if (!controlCheckBox)
        return;
    outputField = document.getElementById(outputField).value = inputField.value.replace(/[^a-zA-Z0-9]/g,'_').toLowerCase();
}

function trim_special_chars(inputField)
{
    inputField.value = inputField.value.replace(/[^a-zA-Z0-9]/g,'_').toLowerCase();
}

function change_state(inputField, state/*OPCIONAL*/, controlCheckBox/*OPCIONAL*/)
{
    //Asignación de valores de por defecto
    controlCheckBox = typeof controlCheckBox !== 'undefined' ? document.getElementById(controlCheckBox) : null;
    inputField = typeof inputField !== 'string' ? inputField : document.getElementById(inputField);
    
    state = typeof state !== 'undefined' ? !state : false;
    
    if (controlCheckBox !== null)
        controlCheckBox.checked = state;
    inputField.readOnly = state;
}