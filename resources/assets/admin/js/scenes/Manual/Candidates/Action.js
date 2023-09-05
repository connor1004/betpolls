import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack, Form, 
  Button, TextField,
  Select
} from '@shopify/polaris';

class Action extends Component {
  constructor(props) {
    super(props);
    this.handleSearch = this.handleSearch.bind(this);
    this.formikRef = React.createRef();
  }

  componentWillReceiveProps(props) {
    const formikRef = this.formikRef.current;
    if (formikRef) {
      if (props.search !== this.props.search) {
        formikRef.setValues(props.search);
      }
    }
  }

  async handleSearch(values) {
    const {
      onSearch
    } = this.props;
    await onSearch(values);
  }

  render() {
    const {
      search
    } = this.props;
    return (
      <Fragment>
        <Formik
          ref={this.formikRef}
          initialValues={{
            category_id: '' || search.category_id,
            candidate_type_id: '' || search.candidate_type_id,
            name: '' || search.name,
            inactive: !!search.inactive
          }}
          onSubmit={this.handleSearch}
          render={({
            values,
            setFieldValue,
            handleSubmit,
            errors
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
                  <Select
                    options={this.props.categories}
                    value={values.category_id}
                    onChange={async (value) => {
                      await setFieldValue('category_id', value);
                      handleSubmit();
                    }}
                  />
                </Stack.Item>
                <Stack.Item
                  fill
                >
                  <Select
                    options={this.props.candidateTypes}
                    value={values.candidate_type_id}
                    onChange={async (value) => {
                      await setFieldValue('candidate_type_id', value);
                      handleSubmit();
                    }}
                  />
                </Stack.Item>
                <TextField
                  placeholder="Name"
                  value={values.name}
                  onChange={(value) => {
                    setFieldValue('name', value);
                  }}
                  error={errors.name}
                />
                <Stack.Item>
                  <Button
                    submit
                    primary
                    icon="search"
                  >
                      Search
                  </Button>
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
  categories: [],
  areas: [],
  onSearch: () => {}
};

export default Action;
