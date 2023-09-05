import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack, Form,
  Button, ButtonGroup, Select
} from '@shopify/polaris';
import DatePicker from 'react-datepicker';
import moment from 'moment';

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
      category_id: values.category_id,
      subcategory_id: values.subcategory_id,
      start_at: moment(values.start_at).format('YYYY-MM-DD'),
      end_at: moment(values.end_at).format('YYYY-MM-DD')
    };
  }

  paramsToFormValues(params) {
    return {
      inactive: params.inactive === 'true',
      category_id: params.category_id,
      subcategory_id: params.subcategory_id || '0',
      start_at: moment(params.start_at).toDate(),
      end_at: moment(params.end_at).toDate()
    };
  }

  render() {
    const {
      search
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
                    options={this.props.subcategories}
                    value={values.subcategory_id}
                    onChange={async (value) => {
                      await setFieldValue('subcategory_id', value);
                      handleSubmit();
                    }}
                  />
                </Stack.Item>
                <Stack.Item>
                  <Stack>
                    <Stack.Item>
                      <DatePicker
                        selected={values.start_at}
                        selectsStart
                        startDate={values.start_at}
                        endDate={values.end_at}
                        // showTimeSelect
                        timeIntervals={5}
                        onChange={async (value) => {
                          await setFieldValue('start_at', value);
                          await setFieldValue('end_at', value);
                          await handleSubmit();
                        }}
                      />
                    </Stack.Item>
                    <Stack.Item>
                      <DatePicker
                        // readOnly
                        selected={values.end_at}
                        selectsEnd
                        startDate={values.start_at}
                        endDate={values.end_at}
                        timeIntervals={5}
                        onChange={async (value) => {
                          await setFieldValue('end_at', value);
                          await handleSubmit();
                        }}
                      />
                    </Stack.Item>
                  </Stack>
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
  onSearch: () => {},
};

export default Action;
