// todo: React not used yet

var FormComponent = React.createClass({
    render: function() {
        return (
            <form method="post" class="referencing branded" id="generic_step_form" novalidate>
                {this.props.children}
            </form>
        );
    }
});

FormComponent.Row = React.createClass({
    render: function() {
        var errorClass = (this.props.hasError) ? ' has-error' : '';

        return (
            <div class="form-group{ errorClass }">
                {this.props.children}
            </div>
        );
    }
});

FormComponent.Label = React.createClass({
    render: function() {
        return (
            <label class="control-label" for="{}">{ this.props.labelText }</label>
        );
    }
});

FormComponent.Input = React.createClass({
    render: function() {
        switch (this.props.type) {
            case 'text':
            default:
                return (
                    <input type="text" id="{ this.props.id }" name="{ this.props.name }" class="form-control" />
                );
        }
    }
});

// Do magical stuffs
//React.render(
//    <FormComponent />,
//    document.getElementById('generic_step_form')
//);