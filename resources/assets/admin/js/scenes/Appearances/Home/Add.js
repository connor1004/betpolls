import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Stack,
  Form, FormLayout,
  InlineError, Button
} from '@shopify/polaris';

import Select from 'react-select';

import OptionsHelper from '../../../helpers/OptionsHelper';

class Add extends Component {
  constructor(props) {
    super(props);
    this.formikRef = null;
    this.handleAdd = this.handleAdd.bind(this);
  }

  formValuesToParams(values) {
    return parseInt(values.league.value, 0);
  }

  paramsToFormValues(params) {
    const {
      leagues
    } = this.props;
    return OptionsHelper.getOption(params, leagues);
  }

  async handleAdd(values, bags) {
    const {
      onAdd
    } = this.props;
    onAdd(this.formValuesToParams(values), bags, this);
  }

  render() {
    const {
      leagues
    } = this.props;

    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          league: ''
        }}
        validationSchema={
          Yup.object().shape({
            league: Yup.object().required('League is required!')
          })
        }
        onSubmit={this.handleAdd}
        render={({
          values,
          errors,
          status,
          touched,
          setFieldValue,
          handleSubmit,
          handleBlur,
          isSubmitting
        }) => (
          <Form onSubmit={handleSubmit}>
            <FormLayout>
              {status && <InlineError message={status} />}
              <Stack>
                <Stack.Item fill>
                  <FormLayout>
                    <FormLayout.Group>
                      <Select
                        placeholder="League"
                        classNamePrefix="react-select"
                        indicatorSeparator={null}
                        options={leagues}
                        value={values.league}
                        onBlur={handleBlur}
                        onChange={async (value) => {
                          await setFieldValue('league', value);
                        }}
                      />
                      {(touched.status && !!errors.status) && <InlineError message={errors.status} />}
                    </FormLayout.Group>
                  </FormLayout>
                </Stack.Item>
                <Stack.Item>
                  <Button
                    loading={isSubmitting}
                    submit
                    primary
                    icon="circlePlus"
                    >
                      Add
                  </Button>
                </Stack.Item>
              </Stack>
            </FormLayout>
          </Form>
        )}
      />
    );
  }
}

Add.defaultProps = {
  onAdd: () => {}
};

export default Add;
