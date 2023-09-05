import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Stack,
  Form, FormLayout,
  InlineError, Button, ButtonGroup, ResourceList, SkeletonBodyText
} from '@shopify/polaris';
import Select from 'react-select';

import OptionsHelper from '../../../helpers/OptionsHelper';

class Item extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDeleting: false,
      isEditMode: false,
      showDeleteConfirm: false
    };
    this.formikRef = null;
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.toggleEditMode = this.toggleEditMode.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  toggleEditMode() {
    this.setState({
      isEditMode: !this.state.isEditMode
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  formValuesToParams(values) {
    return parseInt(values.league.value, 0);
  }

  paramsToFormValues(params) {
    const {
      leagues
    } = this.props;
    return {
      league: OptionsHelper.getOption(params, leagues)
    };
  }

  async handleEdit(values, bags) {
    const {
      onEdit, index
    } = this.props;
    await onEdit(index, this.formValuesToParams(values), bags, this);
  }

  async handleDelete() {
    const {
      onDelete, index
    } = this.props;
    await onDelete(index, this);
  }

  renderEditMode() {
    const {
      data, leagues
    } = this.props;
    return (
      <Formik
        ref={this.formikRef}
        initialValues={
          this.paramsToFormValues(data)
        }
        validationSchema={
          Yup.object().shape({
            league: Yup.object().required('League is required!')
          })
        }
        onSubmit={this.handleEdit}
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
          <Card>
            <ResourceList.Item>
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
                      <ButtonGroup segmented>
                        <Button
                          loading={isSubmitting}
                          submit
                          primary
                          icon="checkmark"
                        />
                        <Button
                          onClick={this.toggleEditMode}
                          icon="cancelSmall"
                        />
                      </ButtonGroup>
                    </Stack.Item>
                  </Stack>
                </FormLayout>
              </Form>
            </ResourceList.Item>
          </Card>
        )}
      />
    );
  }

  renderViewMode() {
    const {
      data
    } = this.props;
    const values = this.paramsToFormValues(data);

    const {
      showDeleteConfirm, isDeleting
    } = this.state;

    return (
      <Card>
        <ResourceList.Item>
          {
            showDeleteConfirm ? (
              <Stack>
                <Stack.Item fill>
                  {`Do you want to delete ${values.league.label}?`}
                </Stack.Item>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      primary
                      loading={isDeleting}
                      onClick={this.handleDelete}
                    >
                      Yes
                    </Button>
                    <Button
                      disabled={isDeleting}
                      onClick={this.toggleDeleteConfirm}
                    >
                      No
                    </Button>
                  </ButtonGroup>
                </Stack.Item>
              </Stack>
            ) : (
              <Stack>
                <Stack.Item fill>
                  {values.league.label}
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '140px' }}>
                    <ButtonGroup segmented>
                      <Button
                        primary
                        icon="products"
                        onClick={this.toggleEditMode}
                      />
                      <Button
                        destructive
                        icon="delete"
                        onClick={this.toggleDeleteConfirm}
                      />
                    </ButtonGroup>
                  </div>
                </Stack.Item>
              </Stack>
            )
          }
        </ResourceList.Item>
      </Card>
    );
  }

  render() {
    const {
      isLoading
    } = this.props;
    const {
      isEditMode
    } = this.state;
    if (isLoading) {
      return (
        <Card>
          <ResourceList.Item>
            <SkeletonBodyText />
          </ResourceList.Item>
        </Card>
      );
    }
    return isEditMode ? this.renderEditMode() : this.renderViewMode();
  }
}

Item.defaultProps = {
  isLoading: false,
  onEdit: () => {},
  onDelete: () => {},
  onToggleActive: () => {}
};

export default Item;
