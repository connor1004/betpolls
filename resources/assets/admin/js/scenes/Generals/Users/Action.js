import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack, Form,
  Button, ButtonGroup, Checkbox,
  TextField
} from '@shopify/polaris';
import Select from 'react-select';

import { BOOLEAN_OPTIONS } from '../../../configs/options';
import OptionsHelper from '../../../helpers/OptionsHelper';


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
      confirmed: values.confirmed ? values.confirmed.value : '',
      search: values.search,
      latest: values.latest
    };
  }

  paramsToFormValues(params) {
    return {
      inactive: params.inactive === 'true',
      confirmed: OptionsHelper.getOption(params.confirmed, BOOLEAN_OPTIONS),
      search: params.search,
      latest: params.latest === 'true'
    };
  }

  render() {
    const {
      search, onToggleAdd
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
                  <Select
                    placeholder="Confirmed"
                    classNamePrefix="react-select"
                    indicatorSeparator={null}
                    options={BOOLEAN_OPTIONS}
                    value={values.confirmed}
                    onChange={async (value) => {
                      await setFieldValue('confirmed', value);
                      await handleSubmit();
                    }}
                  />
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
                <Stack.Item
                  fill
                >
                  <Checkbox
                    checked={values.latest}
                    label="Order by latest"
                    onChange={async (value) => {
                      await setFieldValue('latest', value);
                      await handleSubmit();
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
                      onClick={onToggleAdd}
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
  onToggleAdd: () => {}
};

export default Action;
