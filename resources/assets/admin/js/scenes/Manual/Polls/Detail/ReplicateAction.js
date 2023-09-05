import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack, Form, Modal, TextContainer,
  Button, ButtonGroup,
  Select, Card
} from '@shopify/polaris';

class ReplicateAction extends Component {
  constructor(props) {
    super(props);
    this.state = {
      showReplicateConfirm: false,
      isReplicating: false
    };
    this.handleReplicate = this.handleReplicate.bind(this);
    this.toggleReplicateConfirm = this.toggleReplicateConfirm.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.formikRef = React.createRef();
    this.replicate_id = '0';
  }

  componentWillReceiveProps(props) {
    const formikRef = this.formikRef.current;
    if (formikRef) {
      if (props.search !== this.props.search) {
        formikRef.setValues(props.search);
      }
    }
  }

  handleSubmit(values, bags) {
    if (values.replicate_id == '0') {
      return;
    }
    bags.setSubmitting(false);
    this.replicate_id = values.replicate_id;
    this.toggleReplicateConfirm();
  }

  toggleReplicateConfirm() {
    this.setState({
      showReplicateConfirm: !this.state.showReplicateConfirm
    });
  }

  async handleReplicate() {
    this.setState({
      showReplicateConfirm: false,
      isReplicating: true
    });
    const {
      onReplicate
    } = this.props;
    await onReplicate(this.replicate_id);
    this.setState({
      isReplicating: false
    });
  }

  render() {
    const {
      isReplicating, showReplicateConfirm
    } = this.state;
    return (
      <Card title="Replicate" sectioned>
        <Modal
          open={showReplicateConfirm}
          onClose={this.toggleReplicateConfirm}
          title="Replicate data"
          primaryAction={{
            content: 'OK',
            onAction: this.handleReplicate
          }}
          secondaryActions={[
            {
              content: 'Cancel',
              onAction: this.toggleReplicateConfirm
            }
          ]}
        >
          <Modal.Section>
            <TextContainer>
              Are you sure you want to replicate the selected poll content?
              The previous poll content will be lost.
            </TextContainer>
          </Modal.Section>
        </Modal>
        <Formik
          ref={this.formikRef}
          initialValues={{
            replicate_id: this.props.replicate_id
          }}
          onSubmit={this.handleSubmit}
          render={({
            values,
            setFieldValue,
            handleSubmit
          }) => (
            <Form onSubmit={handleSubmit}>
              <Stack>
                <Stack.Item
                  fill
                >
                  <Select
                    options={this.props.replicates}
                    value={values.replicate_id}
                    onChange={async (value) => {
                      await setFieldValue('replicate_id', value);
                    }}
                  />
                </Stack.Item>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      submit
                      primary
                      icon="import"
                      loading={isReplicating}
                    >
                        Replicate
                    </Button>
                  </ButtonGroup>
                </Stack.Item>
              </Stack>
            </Form>
          )}
        />
      </Card>
    );
  }
}

ReplicateAction.defaultProps = {
  replicates: [],
  onReplicate: () => {}
};

export default ReplicateAction;
