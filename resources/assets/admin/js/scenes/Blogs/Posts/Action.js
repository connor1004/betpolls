import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack,
  Form,
  Button,
  ButtonGroup,
  TextField
} from '@shopify/polaris';

class Action extends Component {
  constructor(props) {
    super(props);

    this.handleSearch = this.handleSearch.bind(this);
    this.formikRef = React.createRef();
  }

  componentWillReceiveProps(props) {
    this.formikRef.current.setValues(this.paramsToFormValues(props.search));
  }

  async handleSearch(values) {
    const params = this.formValuesToParams(values);
    const {
      onSearch
    } = this.props;
    await onSearch(params);
  }

  formValuesToParams(values) {
    return {
      inactive: values.inactive,
      search: values.search,
      post_type: values.post_type
    };
  }

  paramsToFormValues(params) {
    return {
      inactive: params.inactive === 'true',
      search: params.search,
      post_type: params.post_type
    };
  }

  render() {
    const {
      search, onAdd
    } = this.props;

    return (
      <Fragment>
        <Formik
          ref={this.formikRef}
          initialValues={this.paramsToFormValues(search)}
          onSubmit={this.handleSearch}
          render={({
            values,
            setFieldValue,
            handleSubmit
          }) => (
            <Form onSubmit={handleSubmit}>
              <Stack>
                <Stack.Item>
                  <Button
                    destructive={values.inactive}
                    primary={!values.inactive}
                    onClick={async () => {
                      await setFieldValue('inactive', !values.inactive);
                      handleSubmit();
                    }}
                    icon={values.inactive ? 'cancelSmall' : 'checkmark'}
                  >
                    { values.inactive ? 'Inactive' : 'Active' }
                  </Button>
                </Stack.Item>
                <Stack.Item
                  fill
                >
                  <TextField
                    placeholder="Search"
                    value={values.search}
                    onChange={async (value) => {
                      await setFieldValue('search', value);
                    }}
                  />
                </Stack.Item>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      submit
                      primary
                      icon="search"
                    >
                        Search
                    </Button>
                    <Button
                      icon="circlePlus"
                      onClick={onAdd}
                    >
                        Add New
                    </Button>
                  </ButtonGroup>
                </Stack.Item>
              </Stack>
            </Form>
          )}
        />
      </Fragment>
    );
  }
}

Action.defaultProps = {
  isSearching: false,
  search: {},
  onSearch: () => {},
  onAdd: () => {}
};

export default Action;
