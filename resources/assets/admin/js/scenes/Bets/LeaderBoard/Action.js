import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack, Form,
  TextField, Button, ButtonGroup
} from '@shopify/polaris';

class Action extends Component {
  constructor(props) {
    super(props);
    this.handleCalculateTotal = this.handleCalculateTotal.bind(this);
  }

  async handleCalculateTotal(values, bags) {
    const {
      onCalculateTotal
    } = this.props;
    await onCalculateTotal(values);
    bags.setSubmitting(false);
  }

  render() {
    return (
      <Fragment>
        <Formik
          ref={this.formikRef}
          initialValues={{
            
          }}
          onSubmit={this.handleCalculateTotal}
          render={({
            values,
            errors,
            setFieldValue,
            handleSubmit,
            isSubmitting
          }) => (
            <Form onSubmit={handleSubmit}>
              <Stack>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      loading={isSubmitting}
                      submit
                      primary
                      icon="refresh"
                    >
                        Calculate Total Score
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
  onCalculateTotal: () => {}
};

export default Action;
