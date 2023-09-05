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
    this.state = {
      showPullConfirm: false,
      isPulling: false
    };
    this.handleSearch = this.handleSearch.bind(this);
    this.handlePull = this.handlePull.bind(this);
    this.togglePullConfirm = this.togglePullConfirm.bind(this);
  }

  async handleSearch(values) {
    const {
      onSearch
    } = this.props;
    await onSearch(values);
  }

  togglePullConfirm() {
    this.setState({
      showPullConfirm: !this.state.showPullConfirm
    });
  }

  async handlePull() {
    this.setState({
      showPullConfirm: false,
      isPulling: true
    });
    const {
      onPull
    } = this.props;
    await onPull();
    this.setState({
      isPulling: false
    });
  }

  render() {
    const {
      search
    } = this.props;
    const {
      isPulling, showPullConfirm
    } = this.state;
    return (
      <Fragment>
        <Modal
          open={showPullConfirm}
          onClose={this.togglePullConfirm}
          title="Pull data"
          primaryAction={{
            content: 'OK',
            onAction: this.handlePull
          }}
          secondaryActions={[
            {
              content: 'Cancel',
              onAction: this.togglePullConfirm
            }
          ]}
        >
          <Modal.Section>
            <TextContainer>
              Are you sure you want to pull data from deportes?
            </TextContainer>
          </Modal.Section>
        </Modal>
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
                    <Button
                      onClick={this.togglePullConfirm}
                      icon="import"
                      loading={isPulling}
                    >
                        Pull
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
  onPull: () => {}
};

export default Action;
