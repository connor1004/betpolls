import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack, Form, Modal, TextContainer,
  Button, ButtonGroup,
  Select
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
            sport_category_id: '' || search.sport_category_id,
            inactive: !!search.inactive
          }}
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
                    value={values.sport_category_id}
                    onChange={async (value) => {
                      await setFieldValue('sport_category_id', value);
                      handleSubmit();
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
                      button
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
  categories: [],
  onSearch: () => {},
  onPull: () => {}
};

export default Action;
