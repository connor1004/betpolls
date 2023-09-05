import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Checkbox,
  Form, FormLayout,
  InlineError,
  TextField, Button
} from '@shopify/polaris';

class Add extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleAdd = this.handleAdd.bind(this);
  }

  async handleAdd(values, bags) {
    const {
      onAdd
    } = this.props;
    onAdd(values, bags, this);
  }

  render() {
    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          name: '',
          name_es: '',
          published: 0
        }}
        validationSchema={
          Yup.object().shape({
            name: Yup.string().required('Name is required!'),
            name_es: Yup.string().required('Name ES is required!')
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
          <Card sectioned>
            <Form onSubmit={handleSubmit}>
              {status && <InlineError message={status} />}
              <FormLayout>
                <FormLayout.Group>
                  <Checkbox
                    label="Published"
                    checked={values.published == 1}
                    onChange={(value) => {
                      setFieldValue('published', value === true ? 1 : 0);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Name"
                    placeholder="Name"
                    name="name"
                    value={values.name}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('name', value);
                    }}
                    error={touched.name && errors.name}
                  />
                  <TextField
                    label="Name ES"
                    placeholder="Name ES"
                    name="name_es"
                    value={values.name_es}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('name_es', value);
                    }}
                    error={touched.name_es && errors.name_es}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <Button
                    loading={isSubmitting}
                    submit
                    primary
                    icon="circlePlus"
                    >
                      Add
                  </Button>
                </FormLayout.Group>
              </FormLayout>
            </Form>
          </Card>
        )}
      />
    );
  }
}

Add.defaultProps = {
  onAdd: () => {}
};

export default Add;
