import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack, Form, Modal, TextContainer,
  TextField, Button, ButtonGroup
} from '@shopify/polaris';

class Action extends Component {
  constructor(props) {
    super(props);
    this.handleSearch = this.handleSearch.bind(this);
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
            name: search.name || '',
            inactive: !!search.inactive
          }}
          onSubmit={this.handleSearch}
          render={({
            values,
            errors,
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
                    placeholder="Name"
                    value={values.name}
                    onChange={(value) => {
                      setFieldValue('name', value);
                    }}
                    error={errors.name}
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
  onSearch: () => {}
};

export default Action;
